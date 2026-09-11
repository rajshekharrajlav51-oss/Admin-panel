<div>
    @php
        $isDisabled = filter_var($disabled ?? false, FILTER_VALIDATE_BOOLEAN);
        $isMultiple = filter_var($multiple ?? false, FILTER_VALIDATE_BOOLEAN);
    @endphp
    <input
        type="file"
        class="form-control"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        data-image-url="{{ $imageUrl ?? '' }}"
        @disabled($isDisabled)
        @if($isMultiple) multiple @endif
    />
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        const input = document.querySelector('[name="{{ $name }}"]');
        if (input) {
            let imageUrl = input.getAttribute('data-image-url');
            FilePond.create(input, {
                allowImagePreview: true,
                instantUpload: false,
                acceptedFileTypes: ['image/*'],
                credits: false,
                storeAsFile: true,
                files: imageUrl ? [{
                    source: imageUrl, options: {
                        type: 'remote'
                    }
                }] : []
            });
        }
    });
</script>
