
!function (global, $) {
    'use strict';

    function CustomAjaxGroupSelector($element, options) {
        var my = this;
        options = $.extend({
            'mode': 'choose'
        }, options);

        my.options = options;

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupEvents();

    }

    CustomAjaxGroupSelector.prototype = Object.create(ConcreteAjaxSearch.prototype);

    CustomAjaxGroupSelector.prototype.setupEvents = function () {
        var my = this;
        ConcreteEvent.fire('CustomGroupSearch', my);
    };

    CustomAjaxGroupSelector.prototype.updateResults = function (result) {
        var my = this, $e = my.$element;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        if (my.options.mode == 'choose') {
            // hide the checkbox since they're pointless here.
            $e.find('.ccm-search-results-checkbox').parent().remove();
            // hide the bulk item selector.
            $e.find('select[data-bulk-action]').parent().remove();
            
            $e.unbind('.concreteGroupSearchHoverGroup');
            $e.on('mouseover.concreteGroupSearchHoverGroup', 'tr[data-group-row]', function () {
                $(this).addClass('ccm-search-select-hover');
            });
            $e.on('mouseout.concreteGroupSearchHoverGroup', 'tr[data-group-row]', function () {
                $(this).removeClass('ccm-search-select-hover');
            });
            $e.unbind('.concreteGroupSearchChooseGroup').on('click.concreteGroupSearchChooseGroup', 'tr[data-group-row]', function () {
                var el = $(this).find('a[data-group-id]');
                ConcreteEvent.publish('AdvancedPickerSelectGroup', {
                    groups:[
                        {
                            gID: el.attr('data-group-id'),
                            gName: el.attr('data-group-name')
                        }
                    ]
                });
                return false;
            });
        }
    }

    CustomAjaxGroupSelector.prototype.handleSelectedBulkAction = function (value, type, $option, $items) {
        if (value == 'choose-selected') {
            var url, my = this, itemIDs = [];
            
            var itemData = [];
            $.each($items, function (i, checkbox) {
                var element = $(checkbox).parents('tr').find('a[data-group-id]');
                itemData.push({
                    gID: element.data('group-id'),
                    gName: element.data('group-name')
                });
            });
           
            ConcreteEvent.publish('AdvancedPickerSelectGroup', {groups:itemData});
            
        }else{
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
        }
    }

    /**
     * Static Methods
     */
    CustomAjaxGroupSelector.launchDialog = function(callback,opts) {
        var w = $(window).width() - 53;

        var options = {
            multipleSelection: false
        };

        $.extend(options, opts);

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + "/advanced_asset_pickers/group_search",
            modal: true,
            data: options,
            title: "Choose Group",
            onClose: function() {
                ConcreteEvent.fire('CustomGroupSelectorClose');
            },
            onOpen: function() {
                ConcreteEvent.unsubscribe('AdvancedPickerSelectGroup');
                ConcreteEvent.subscribe('AdvancedPickerSelectGroup', function(e, data) {
                    jQuery.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    CustomAjaxGroupSelector.getGroupDetails = function(gID, callback) {
        // $.ajax({
        //     type: 'post',
        //     dataType: 'json',
        //     url: CCM_DISPATCHER_FILENAME + '/advanced_asset_pickers/group_search/get_groups',
        //     data: {'gID': gID},
        //     error: function(r) {
        //         ConcreteAlert.dialog('Error', r.responseText);
        //     },
        //     success: function(r) {
        //         callback(r);
        //     }
        // });
    };
    
    // jQuery Plugin
    $.fn.customAjaxGroupSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new CustomAjaxGroupSelector($(this), options);
        });
    }

    global.CustomAjaxGroupSelector = CustomAjaxGroupSelector;

}(this, $);
