jQuery(document).ready(function($) {
    let mediaUploader;
    
    // Toggle fields based on media type
    $('#tipo_midia').on('change', function() {
        const tipo = $(this).val();
        if (tipo === 'video') {
            $('#upload-container').hide();
            $('#video-container').show();
        } else {
            $('#upload-container').show();
            $('#video-container').hide();
        }
    });
    
    // Handle media upload
    $('#add-midia').on('click', function(e) {
        e.preventDefault();
        
        if (!mediaUploader) {
            mediaUploader = wp.media({
                title: 'Selecionar Mídia',
                button: {
                    text: 'Usar esta mídia'
                },
                multiple: true
            });
        }
        
        mediaUploader.on('select', function() {
            const attachments = mediaUploader.state().get('selection').toJSON();
            attachments.forEach(attachment => {
                const preview = `
                    <div class="midia-item">
                        <img src="${attachment.url}" alt="Preview">
                        <input type="hidden" name="midias[]" value="${attachment.url}">
                        <button type="button" class="remove-midia">×</button>
                    </div>
                `;
                $('#midia-uploads').append(preview);
            });
        });
        
        mediaUploader.open();
    });
    
    // Remove media item
    $(document).on('click', '.remove-midia', function() {
        $(this).closest('.midia-item').remove();
    });
});