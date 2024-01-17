import "./CodeBox/codebox.min.js"

document.addEventListener('DOMContentLoaded', function () {

    const contentEditorElement = document.getElementById('contentEditor')
    const editor = new EditorJS({
        holder: 'contentEditor',
        tools: {
            header: Header,
            list: List,
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '/admin/editorjs/upload-file', // Your backend file uploader endpoint
                        byUrl: '/admin/editorjs/fetch-url', // Your endpoint that provides uploading by Url
                    }
                }
            },
            codeBox: {
                class: CodeBox,
                config: {
                    themeURL: 'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.18.1/build/styles/dracula.min.css', // Optional
                    themeName: 'atom-one-dark', // Optional
                    useDefaultTheme: 'light' // Optional. This also determines the background color of the language select drop-down
                }
            },
        },
        data: contentEditorElement.dataset.value !== '' ? JSON.parse(contentEditorElement.dataset.value) : null
    });


    const form = document.querySelector('form')

    form.addEventListener('submit', function (e) {
        editor.save().then((outputData) => {
            document.getElementById('content').value = JSON.stringify(outputData)
        }).catch((error) => {
            console.log('Saving failed: ', error)
        });

        form.submit()
    })


    //------------------------------------------------------------
    //----------------------- DRAG AND DROP ----------------------
    //------------------------------------------------------------

    var dropzone = document.getElementById('cover-dropzone');

    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-600');
    });

    dropzone.addEventListener('dragleave', e => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-600');
    });

    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-600');
        var file = e.dataTransfer.files[0];
        displayPreview(file);
    });

    var input = document.getElementById('dropzone-file');

    input.addEventListener('change', e => {
        var file = e.target.files[0];
        displayPreview(file);
    });

    function displayPreview(file) {
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            var preview = document.getElementById('preview');
            preview.src = reader.result;
            preview.classList.remove('hidden');
        };
    }

})
