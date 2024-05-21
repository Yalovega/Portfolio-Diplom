$(document).ready(function() {
    $('.telegram-form').on('submit', function(event) {
        event.stopPropagation();
        event.preventDefault();

        let form = this,
            submitButton = $('.submit', form),
            data = new FormData(),
            files = $('input[type=file]', form);

        // Update submit button text and disable inputs during submission
        submitButton.val('Отправка...');
        $('input, textarea', form).attr('disabled', '');

        // Append form data
        data.append('name', $('[name="name"]', form).val());
        data.append('phone', $('[name="phone"]', form).val());
        data.append('email', $('[name="email"]', form).val());
        data.append('text', $('[name="text"]', form).val());
        data.append('file', $('[name="file"]', form).val());

        // Append files if any
        files.each(function(index, fileInput) {
            let fileList = fileInput.files;
            if (fileList) {
                $.each(fileList, function(index, file) {
                    data.append('file_' + index, file);
                });
            }
        });

        // AJAX request
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            xhr: function() {
                let myXhr = $.ajaxSettings.xhr();

                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            let percentage = (e.loaded / e.total) * 100;
                            submitButton.html(percentage.toFixed(0) + '%');
                        }
                    }, false);
                }

                return myXhr;
            },
            success: function(response) {
                // Handle success response
                console.log('Success:', response);
            },
            error: function(jqXHR, textStatus) {
                // Handle error response
                console.error('Error:', textStatus);
            },
            complete: function() {
                // Re-enable inputs and reset form after completion
                $('input, textarea', form).removeAttr('disabled');
                submitButton.val('Отправить');
                form.reset();
                console.log('Complete');
            }
        });

        return false;
    });
});
