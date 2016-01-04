
!function (global, $) {
    'use strict';

    function CustomAjaxUserSelector($element, options) {
        var my = this;
        options = $.extend({
            'mode': 'choose'
        }, options);

        my.options = options;

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupEvents();

    }

    CustomAjaxUserSelector.prototype = Object.create(ConcreteAjaxSearch.prototype);

    CustomAjaxUserSelector.prototype.setupEvents = function () {
        var my = this;
        ConcreteEvent.fire('CustomUserSearch', my);
    };

    CustomAjaxUserSelector.prototype.updateResults = function (result) {
        var my = this, $e = my.$element;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        if (my.options.mode == 'choose') {
            // hide the checkbox since they're pointless here.
            $e.find('.ccm-search-results-checkbox').parent().remove();
            // hide the bulk item selector.
            $e.find('select[data-bulk-action]').parent().remove();
            
            $e.unbind('.concreteUserSearchHoverUser');
            $e.on('mouseover.concreteUserSearchHoverUser', 'tr[data-user-row]', function () {
                $(this).addClass('ccm-search-select-hover');
            });
            $e.on('mouseout.concreteUserSearchHoverUser', 'tr[data-user-row]', function () {
                $(this).removeClass('ccm-search-select-hover');
            });
            $e.unbind('.concreteUserSearchChooseUser').on('click.concreteUserSearchChooseUser', 'tr[data-user-row]', function () {
                ConcreteEvent.publish('AdvancedPickerSelectUser', {
                    instance: my,
                    uID: $(this).attr('data-user-id'),
                    title: $(this).attr('data-user-name')
                });
                return false;
            });
        }
    }

    CustomAjaxUserSelector.prototype.handleSelectedBulkAction = function (value, type, $option, $items) {
        if (value == 'choose-selected') {
            var url, my = this, itemIDs = [];
            $.each($items, function (i, checkbox) {
                itemIDs.push($(checkbox).val());
            });
            
            ConcreteEvent.publish('AdvancedPickerSelectUser', { uID: itemIDs });
            
        }else{
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
        }
    }

    /**
     * Static Methods
     */
    CustomAjaxUserSelector.launchDialog = function(callback,opts) {
        var w = $(window).width() - 53;

        var options = {
            multipleSelection: false
        };

        $.extend(options, opts);

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + "/advanced_asset_pickers/user_search",
            modal: true,
            data: options,
            title: "Choose User",
            onClose: function() {
                ConcreteEvent.fire('CustomUserSelectorClose');
            },
            onOpen: function() {
                ConcreteEvent.unsubscribe('AdvancedPickerSelectUser');
                ConcreteEvent.subscribe('AdvancedPickerSelectUser', function(e, data) {
                    jQuery.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    CustomAjaxUserSelector.getUserDetails = function(uID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/advanced_asset_pickers/user_search/get_users',
            data: {'uID': uID},
            error: function(r) {
                ConcreteAlert.dialog('Error', r.responseText);
            },
            success: function(r) {
                callback(r);
            }
        });
    };
    
    // jQuery Plugin
    $.fn.customAjaxUserSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new CustomAjaxUserSelector($(this), options);
        });
    }

    global.CustomAjaxUserSelector = CustomAjaxUserSelector;

}(this, $);
