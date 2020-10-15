/**
 * AJAX script for Go Further
 */
(function($) {
    $('.get-related-posts').on('click', function(e) {

        e.preventDefault();

        $('a.get-related-posts').remove();
        //Show animation loader
        $('.ajax-loader').show();

        //Get REST Url and post ID from WordPress
        var json_url = postdata.json_url;
        var post_id = postdata.post_id;

        //console.log(json_url);
        //console.log(post_id);

        // The Ajax
        $.ajax({
                url: json_url,
                type: 'GET',
                dataType: 'json',
                contentType: 'application/json;charset=utf-8'
            })

            .done(function(data) {

                //console.log(data);

                $('#related-posts').append('<h1 class="related-header">Related Posts:</h1>');
                //Loop through each of the relation posts
                $.each(data, function(index, object) {

                    if (object.id == post_id) {
                        return;
                    }

                    var feat_img = '';
                    if (object.featured_image !== 0) {
                        feat_img = '<figure class="related-featured">' +
                            '<img src="' + object.featured_image_src + '" alt="">' +
                            '</figure>';
                    }

                    //Set up HTML to be added
                    var related_loop = '<aside class="related-post clear">' +
                        '<a href="' + object.link + '">' +
                        '<h1 class="related-post-title">' + object.title.rendered + '</h1>' +
                        '<div class="related-author">by<em>' + object.author_name + '</em></div>' +
                        '<div class="related-excerpt">' +
                        feat_img +
                        object.excerpt.rendered +
                        '</div>' +
                        '</a>' +
                        '</aside><!-- .related-post -->';

                    //Remove animation loader
                    $('.ajax-loader').remove();

                    //Append HTML to existing content
                    $('#related-posts').append(related_loop);

                })
            })

            .fail(function(jqXHR, textStatus, errorThrown) {
                //console.log(textStatus + jqXHR);
            })

            .always(function(jqXHROrData, textStatus, jqXHROrErrorThrown) {
                //console.log(textStatus, jqXHROrData);
            })
    })
})(jQuery);