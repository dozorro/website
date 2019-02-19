<div class="sort-block">
    <div class="sort-block-title">{{ t('tender.search.sort') }}</div>
    <select name="sort" class="indicator-search-form-sorting">
        <option value="dateModified-desc" {{ !empty($filters['sort']) && $filters['order'][0] == 'desc' && $filters['sort'][0] == 'dateModified' ? 'selected' : '' }}>{{ t('tender.search.sort.dateModified_desc') }}</option>
        <option value="value-desc" {{ !empty($filters['sort']) && $filters['order'][0] == 'desc' && $filters['sort'][0] == 'value' ? 'selected' : '' }}>{{ t('tender.search.sort.value_desc') }}</option>
        @if($riskAccess)
            <option value="risk-desc" {{ !empty($filters['sort']) && $filters['order'][0] == 'desc' && $filters['sort'][0] == 'risk' ? 'selected' : '' }}>{{ t('tender.search.sort.risk_desc') }}</option>
        @endif
    </select>
</div>