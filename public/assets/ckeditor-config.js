function ckeditorConfig() {
    return {
        toolbar: [ 
            'heading', 'Alignment', '|', 
            'FontFamily', 'FontSize', 'FontColor', 'FontBackgroundColor', '|',
            'bold', 'italic', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'indent', 'outdent', 'HorizontalLine', '|',
            'blockQuote', 'insertTable', 'mediaEmbed', 'CKFinder', '|',
            'undo', 'redo' ],
        mediaEmbed: {
            previewsInData: true,
        },
        fontSize: {
            options: ['default', 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48],
            supportsAllValues: false,
        },
        image: {
            resizeUnit: 'px',
        },
        ckfinder: {
            uploadUrl: '/ckfinder/connector'
        }
    };
}