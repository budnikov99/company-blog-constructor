function ckeditorConfig() {
    return {
        toolbar: [ 
            'heading', 'Alignment', '|', 
            'FontFamily', 'FontSize', 'FontColor', 'FontBackgroundColor', '|',
            'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'indent', 'outdent', 'HorizontalLine', 'specialCharacters', '|',
            'blockQuote', 'insertTable', 'mediaEmbed', 'CKFinder', '|',
            'undo', 'redo'],
        mediaEmbed: {
            previewsInData: true,
        },
        fontSize: {
            options: ['default', 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48],
            supportsAllValues: false,
        },
        image: {
            resizeUnit: 'px',
            toolbar: [ 'imageStyle:full', 'imageStyle:side', '|', 'imageTextAlternative' ]
        },
        table: {
            contentToolbar: [ 'tableRow', 'tableColumn', 'mergeTableCells' ]
        },
        ckfinder: {
            uploadUrl: '/ckfinder/connector'
        }
    };
}