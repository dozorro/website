<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;

class CommentsLocalUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:localupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Comments local-update: ' . Cron::commentsLocalUpdate($this->badForms2));
    }

    private $badForms2 = [
        'd6e49fa372e5f76f47816fcd2753defe',
        'b3b1b24a3235b0c5e7259fb7c655ec64',
        'e54640aceb2b7f10c3bb39e17a05987c',
        '76a39d3b32e7973ce1b40afd2a1bf6e6',
        'fc086829424e07a78d95407ccd9c721c',
        '05a6871ba7cd3188668d3af3ff528ff3',
        '83598143b11f73a08dc7c3ca947277aa',
        '9f89cfbce2092cb17fcbd5ce5ab358e3',
    ];

    private $badForms = [
        '46080c99cde5093e1c2a96eb43609fc7',
        '3679ceb9972fee92b5c90745057c06e6',
        '5942ed3fb7955f9f251878ba936058ef',
        'eeac5a07268492a20fa55c8ded11504c',
        '742f2bcf67bfa305b152ab407edda480',
        '9d88817d47eedcbbddcb964c7c8c2f11',
        '3328ab91eeb126668f2e83258d612483',
        'c0417b6113d75d98d8f78b54f4d178e3',
        '631935304017c2aceaee348339c60e10',
        '2be1b484a20c10a32e7abdc872b8f4a0',
        '5e4e6e012ed724b1067d5c9717238967',
        '8499fda7a1964c85275b1f7bdaf1f3ee',
        'd34f967b42925954192d056fd9644ff6',
        '9f472f172292e184f744235e52f1d698',
        'aa1f39e60b66e8cd225fcd62a8c6c7da',
        'd58156abb051c0280254950c7fae80aa',
        'cc3d1f7887f63d651445df9002159e31',
        'a2e42349d4854ccc1bdadc3b917e3f5d',
        'c97ae91bc01be7464766fc0ad4baf683',
        'a158c8aa81d9ec361b0de855f4f84050',
        'fca2b132fb55039626279c27553896aa',
        '3bbf9c11104a18e59f53d75451aa0f62',
        '477e2463d5d9acfb01c402cc247e72db',
        '745235bd776fd5993ef2645b88696c95',
        'dca1f5d197bdf59f4b2a500612d825ad',
        'c3b85305d422f7178538f4c2e7f4dee7',
        'e19668bfa5175d692fa3bc570b5a8b3c',
        '01862b077771529dcdb76a0a8965dc1d',
        '9c897bf163cb89fff4370a7bf0e397b0',
        'bd5c0995adc55c90e814b71be8483369',
        'f0f7349e466ae991bbab89e4fef68a26',
        '725538b5d35f3a35020b4a9bc5d212d0',
        'c3f53be0ce4d7790194cfad366fdac49',
        'd71a5af9a4def5fdb2933ac4842c0dcc',
        'b582f288f521d9e2f53069ce21c13cf3',
        'ca5467f9122a2050975c935366c76916',
        '3fbf60441b4c4ddd8edd659d1ee41b68',
        '8879be76855da8779d0110a7002a60ae',
        '82174ff54c298ecdbaea39eb3e0bf4bb',
        'f2d88a7f532e61e65c0072d924f4f934',
        'bfe0f8a83a2d0e4dbe34ba64323dfe59',
        '4f64a62ac917d8fef9abec874b9fa0a1',
        '490ed7359efa595c99b57612c27a4ac9',
        '0e1be8cb6a4cc2d14f1344470930c84c',
        '807fc3ce13de5bc6c4b4b24a092e671e',
        '4f022ec77d7442ae63769da50f893c22',
        '07407a90b29b029e902bb6eac69e1e20',
        '6eb35057796f5d0a3020178d8ed7c614',
        '88e7071334b5324408ff86459edf0bb1',
        '49ee436e2e6b0501555f21ed59a28401',
        '0d69fcf363a99cfc8b1ccefad7af04f8',
        '78913ddcb7d9f22840a6cb425fa83860',
        '497e6951ade9dae2d7791cd4c74ca7a3',
        '11f0268492da9a16853c82d3411f016d',
        '5dc8c1144b7826360b535feb500eae9d',
        'f0b1729276f9d3f0de241d8a0a685517',
        '4404f81258a5276955616b673cebccef',
        'edee42977c790cac925330ff8d590e52',
        '59210279ea9687b1aa7e0d9749d4554c',
        'dfb464fbdede30f2932fbd257e81138e',
        'f47a3f08841f8b8d4ccf8c95252690d5',
        '8c95d033dba049920bfc1df54e8ffa81',
        '6c378b92e4554880220ff43641a4eaa8',
        '397ba7678a298dadb5dc726ff392e6ca',
        '9075f0f6125825ecce4ba8317019b499',
        '1b2ffa5f68513ced03694de44d0a501a',
        'd381fc3af9d7e5171412364d3a991ed8',
        '7672418d659d1c720ffa52e07d0db003',
        '08db73719a70d9e6faf03d045c993c7f',
        '56ff580ef433e66e35cb9f5435d6033f',
        'a7e7e45a9ed0ba69a15ba7dca5f0d4b7',
        '4b4a5d24e180e4fc5b3bdb58ec705e5b',
        'fbe4c4ed822e5a26832899ee079cf1e5',
        'e63401f3101a2215869c4278c8044432',
        'be15031f93baec399ddf31b76c50562c',
        '24ecaa3a5e0a7bca05f5200d81b5bc04',
        '2da8b16c916148bab141619385ddea71',
        '0838079c60127547eed4e0c059e026ff',
        '42453426ca680338abc843e61ee10a95',
        'fddfde15dc49847537aabaf264f87286',
        '2f1f0471249043480cff466a5b387896',
        '07f5d749ba89c4920e170670a4ffaf22',
        'ee47af1e9f736e7fe1165d17ed056162'
    ];
}
