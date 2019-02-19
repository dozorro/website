<?php

namespace App\Classes;

use App;
use GuzzleHttp;
use Illuminate\Support\Facades\Log;
use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;

class JiraApi
{
    private $apiUrl;
    private $jiraApi;
    private $apiLogin;
    private $apiPass;
    private $projectKey;
    private $defaultIssue;
    public $emailFieldId;

    public function __construct()
    {
        $this->apiUrl = env('JIRA_PROJECT_HOST');
        $this->apiLogin = env('JIRA_USER_LOGIN');
        $this->apiPass = env('JIRA_USER_PASSWORD');
        $this->projectKey = env('JIRA_PROJECT_KEY');
        $this->defaultIssue = env('JIRA_ISSUE_TYPE_NAME');
        $this->emailFieldId = env('JIRA_EMAIL_FIELD_ID');

        try {
            $this->jiraApi = new Api(
                $this->apiUrl,
                new Basic($this->apiLogin, $this->apiPass)
            );
        } catch (Api\Exception $e) {
        } catch (\Exception $e) {
            Log::error('Error sending feedback to Jira: ' . $e->getMessage());
        }
    }

    public function getIssueTypesJira()
    {
        try {
            return $this->jiraApi->getIssueTypes();
        } catch (Api\Exception $e) {
        } catch (\Exception $e) {
            Log::error('Error sending feedback to Jira: ' . $e->getMessage());
        }

        return false;
    }

    public function getFieldsJira()
    {
        try {
            return $this->jiraApi->getFields();
        } catch (Api\Exception $e) {
        } catch (\Exception $e) {
            Log::error('Error sending feedback to Jira: ' . $e->getMessage());
        }

        return false;
    }

    public function sendFeedbackToJira($title, $issueType = null, $fields = [])
    {
        try {
            $res = $this->createIssue($this->projectKey, $title, empty($issueType) ? $this->defaultIssue : $issueType, $fields);
            $res = $res->getResult();

            if(!empty($res['errors'])) {
                Log::info('Failed sending feedback to Jira: ' . json_encode($res['errors']));
                return $res['errors'];
            }

            return true;
        } catch (Api\Exception $e) {
        } catch (\Exception $e) {
            Log::error('Error sending feedback to Jira: ' . $e->getMessage());
            return $e->getMessage();
        }

        return false;
    }

    /**
     * Creates an issue.
     *
     * @param string $project_key Project key.
     * @param string $summary Summary.
     * @param string $issue_type Issue type.
     * @param array $options Options.
     *
     * @return Result|false
     */
    public function createIssue($project_key, $summary, $issue_type, array $options = array())
    {
        $issueKey = is_numeric($issue_type) ? 'id' : 'name';

        $default = array(
            'project' => array(
                'key' => $project_key,
            ),
            'summary' => $summary,
            'issuetype' => array(
                $issueKey => $issue_type,
            ),
        );

        $default = array_merge($default, $options);

        return $this->jiraApi->api(Api::REQUEST_POST, '/rest/api/2/issue/', array('fields' => $default));
    }

    public function getScreens()
    {
        return $this->jiraApi->api(Api::REQUEST_GET, '/rest/api/2/screens');
    }

    public function getScreenFields($id)
    {
        return $this->jiraApi->api(Api::REQUEST_GET, '/rest/api/2/screens/'.$id.'/availableFields');
    }
}