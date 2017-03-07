(function( $ ) {
    $.fn.rfPreviewImage = function (dataTargetId) {
        return this.each(function () {
            console.log(dataTargetId);

            var targetId = $(this).data(dataTargetId);
            console.log(targetId);
            var field = $(this);
            $(this).change((function () {
                var files = field.context.files;

                if (targetId[0] != '#') {
                    targetId = '#' + targetId
                }

                if (files && files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(targetId).attr('src', e.target.result);
                    };

                    reader.readAsDataURL(files[0]);
                }
            }));
        });
    };
}( jQuery ));