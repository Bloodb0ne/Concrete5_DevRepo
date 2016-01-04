
!function (global, $) {
    'use strict';

    function CustomAjaxPageSelector($element, options) {
        var my = this;
        options = $.extend({
            'mode': 'choose'
        }, options);

        my.options = options;

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupEvents();

    }

    CustomAjaxPageSelector.prototype = Object.create(ConcreteAjaxSearch.prototype);

    CustomAjaxPageSelector.prototype.setupEvents = function () {
        var my = this;
        ConcreteEvent.fire('CustomPageSearch', my);
    };

    CustomAjaxPageSelector.prototype.updateResults = function (result) {
        var my = this, $e = my.$element;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        if (my.options.mode == 'choose') {
            // hide the checkbox since they're pointless here.
            $e.find('.ccm-search-results-checkbox').parent().remove();
            // hide the bulk item selector.
            $e.find('select[data-bulk-action]').parent().remove();

            $e.unbind('.concretePageSearchHoverPage');
            $e.on('mouseover.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function () {
                $(this).addClass('ccm-search-select-hover');
            });
            $e.on('mouseout.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function () {
                $(this).removeClass('ccm-search-select-hover');
            });
            $e.unbind('.concretePageSearchChoosePage').on('click.concretePageSearchChoosePage', 'tr[data-launch-search-menu]', function () {
                ConcreteEvent.publish('AdvancedPickerSelectPage', {
                    instance: my,
                    cID: $(this).attr('data-page-id'),
                    title: $(this).attr('data-page-name')
                });
                return false;
            });
        }
    }

    CustomAjaxPageSelector.prototype.handleSelectedBulkAction = function (value, type, $option, $items) {
        if (value == 'choose-selected') {
            var url, my = this, itemIDs = [];
            $.each($items, function (i, checkbox) {
                itemIDs.push($(checkbox).val());
            });
            
            ConcreteEvent.publish('AdvancedPickerSelectPage', { cID: itemIDs });
            
        }else{
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
        }
    }

    /**
     * Static Methods
     */
    CustomAjaxPageSelector.launchDialog = function(callback,opts) {
        var w = $(window).width() - 53;

        var options = {
            multipleSelection: false,
        };

        $.extend(options, opts);

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + "/advanced_asset_pickers/page_search",
            modal: true,
            data: options,
            title: "Choose Page",
            onClose: function() {
                ConcreteEvent.fire('CustomPageSelectorClose');
            },
            onOpen: function() {
                ConcreteEvent.unsubscribe('AdvancedPickerSelectPage');
                ConcreteEvent.subscribe('AdvancedPickerSelectPage', function(e, data) {
                    jQuery.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    CustomAjaxPageSelector.getPageDetails = function(cID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/advanced_asset_pickers/page_search/get_pages',
            data: {'cID': cID},
            error: function(r) {
                ConcreteAlert.dialog('Error', r.responseText);
            },
            success: function(r) {
                callback(r);
            }
        });
    };
    
    // jQuery Plugin
    $.fn.customAjaxPageSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new CustomAjaxPageSelector($(this), options);
        });
    }

    global.CustomAjaxPageSelector = CustomAjaxPageSelector;
    // global.CustomAjaxPageSelectorMenu = CustomAjaxPageSelectorMenu;

}(this, $);
