// Moment
moment.locale('fr');

// CKEditor
CKEDITOR.config.contentsCss = $.map($('link'), function (link) {
    if ('stylesheet' == $(link).attr('rel'))
        return $(link).attr('href');
});

// ADCOG context
$.fn.adcogContext = function () {
    // Datepickers
    $(this).find('input.datepicker').datepicker({ //Changement DatePicker pour pouvoir sélectionner années et mois
        changeMonth: true,
        changeYear: true,
        yearRange: "c-50:c+10"
    });


    // Wysiwyg rich contents
    $(this).find('textarea.wysiwyg[data-wysiwyg]').each(function () {
        $(this).ckeditor(CKEDITOR.editorConfig($(this).data('wysiwyg')));
    });

    // Simple select2
    $(this).find('select').each(function () {
        if ($(this).children('option').length < 8) {
            $(this).select2({
                allowClear: !$(this).prop('required'),
                minimumResultsForSearch: -1,
            });
        } else {
            $(this).select2({
                allowClear: !$(this).prop('required'),
            });
        }
    });

    // Select2 tags
    $(this).find('[data-tags]').each(function () {
        $(this).select2({
            tags: []
        });
    });

    // Dates
    $(this).find('abbr[data-timeago]').each(function () {
        $(this).text(moment(1000 * $(this).data('timeago')).fromNow());
    });
    $(this).find('abbr[data-admin-date]').each(function () {
        $(this).text(moment(1000 * $(this).data('admin-date')).fromNow());
    });
    $(this).find('abbr[data-admin-datetime]').each(function () {
        $(this).text(moment(1000 * $(this).data('admin-datetime')).fromNow());
    });

    // Employer selector
    var $employer = $(this).find('input[data-employer-selector]'),
        $parent = $employer.closest('div.form-group').parent();
    $employer.on('select2-selecting', function (event) {
        var $others = $parent
            .children('.form-group')
            .not($(this).closest('div.form-group'));

        if (event.object.hasOwnProperty('city')) {
            $others.hide();
        } else {
            $others.show();
        }
    });
};

// jQuery start
$(document).ready(function () {
    $(document.body).adcogContext();


    $('input[data-ws]').each(function () {
        var val = $(this).val();
        $(this)
            .select2({
                minimumInputLength: 2,
                allowClear: !$(this).prop('required'),
                multiple: false,
                initSelection: function (element, callback) {
                    callback({
                        id: $(element).val(),
                        text: $(element).val()
                    });
                },
                ajax: {
                    url: $(this).data('ws'),
                    dataType: 'json',
                    quietMillis: 100,
                    data: function (term, page) {
                        return {
                            query: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                if ('string' === typeof item) {
                                    return {
                                        id: item,
                                        text: item,
                                    };
                                }

                                return item;
                            }),
                        };
                    }
                },
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                        return {id: term, text: term};
                    }
                },
            })
            .select2('val', val);
    });

    $('[data-autocomplete-ws]').each(function () {
        $(this).select2({
            minimumInputLength: 2,
            tags: [],
            multiple: false,
            tokenSeparators: [','],
            ajax: {
                url: $(this).data('autocomplete-ws'),
                dataType: 'json',
                data: function (term, page) {
                    return {query: term};
                },
                results: function (data, page) {
                    return {results: data};
                },
            },
            initSelection: function (element, callback) {
                var data = [];
                $(element.val().split(',')).each(function (i, item) {
                    data.push({id: item, text: item});
                });
                callback(data);
            },
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return 0 === this.text.localeCompare(term);
                    }).length === 0) {
                    return {id: term, text: term};
                }
            },
        });
    });
    $('[data-tags-ws]').each(function () {
        $(this).select2({
            minimumInputLength: 2,
            tags: [],
            multiple: true,
            tokenSeparators: [','],
            ajax: {
                url: $(this).data('tags-ws'),
                dataType: 'json',
                data: function (term, page) {
                    return {query: term};
                },
                results: function (data, page) {
                    return {results: data};
                },
            },
            initSelection: function (element, callback) {
                var data = [];
                $(element.val().split(',')).each(function (i, item) {
                    data.push({id: item, text: item});
                });
                callback(data);
            },
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return 0 === this.text.localeCompare(term);
                    }).length === 0) {
                    return {id: term, text: term};
                }
            },
        });
    });

    // Images in blog
    $('#blog_read .article .col-sm-10 .content img').each(function () {
        var $img = $(this),
            $ul = $('<ul>').addClass('polaroids list-unstyled'),
            $li = $('<li>').appendTo($ul),
            $a = $('<a>').attr('href', $img.attr('src')).attr('title', $img.attr('alt')).appendTo($li).on('click', function () {
                return hs.expand($(this).get(0));
            });

        $a.append($img.before($ul));
    });
});
