<label class="checkbox-item">
    <input {{ !empty($filters['risk_code']) && in_array($risk->risk_code, $filters['risk_code']) ? 'checked' : '' }}
           {{ !empty($filters['risk_code_all']) && in_array($risk->risk_code, $filters['risk_code_all']) ? 'checked' : '' }}
           type="checkbox" name="risks" value="{{ $risk->risk_code }}" data-text="{{ t('indicators.'.$risk->risk_title) }}" id="risk-{{ $risk->risk_code }}" class="checkbox-default">
    <span class="checkbox-custom"></span>
    <span class="checkbox-text" for="risk-{{ $risk->risk_code }}">{{ t('indicators.'.$risk->risk_title) }}
        @if($risk->description)
        <span class="info">
            <span class="info_icon" style="position: relative;top: 2px;"></span>
            <div class="info_text" style="margin-left: -250px;width: 250px;">
                <div>
                    <p style="font-size: 12px;">
                        {{ $risk->description }}
                    </p>
                </div>
            </div>
        </span>
        @endif
    </span>
</label>