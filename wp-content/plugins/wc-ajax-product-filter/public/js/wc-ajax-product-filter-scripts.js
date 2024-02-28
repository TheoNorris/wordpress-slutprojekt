"use strict";

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) { symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); } keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/**
 * The main js file.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/public/src/js
 * @author     wptools.io
 */
var wcapf_params = wcapf_params || {
  'is_rtl': '',
  'filter_input_delay': '',
  'keyword_filter_delay': '',
  'combobox_display_selected_options': '',
  'combobox_no_results_text': '',
  'combobox_options_none_text': '',
  'search_box_in_default_orderby': '',
  'preserve_hierarchy_accordion_state': '',
  'preserve_soft_limit_state': '',
  'enable_animation_for_filter_accordion': '',
  'filter_accordion_animation_speed': '',
  'filter_accordion_animation_easing': '',
  'enable_animation_for_hierarchy_accordion': '',
  'hierarchy_accordion_animation_speed': '',
  'hierarchy_accordion_animation_easing': '',
  'scroll_to_top_speed': '',
  'scroll_to_top_easing': '',
  'is_mobile': '',
  'reload_on_back': '',
  'found_wcapf': '',
  'wcapf_pro': '',
  'update_document_title': '',
  'use_tippyjs': '',
  'shop_loop_container': '',
  'not_found_container': '',
  'pagination_container': '',
  'orderby_form': '',
  'orderby_element': '',
  'disable_ajax': '',
  'enable_pagination_via_ajax': '',
  'sorting_control': '',
  'attach_combobox_on_sorting': '',
  'loading_animation': '',
  'scroll_window': '',
  'scroll_window_for': '',
  'scroll_window_when': '',
  'scroll_window_custom_element': '',
  'scroll_on': '',
  'scroll_to_top_offset': '',
  'disable_scroll_animation': '',
  'more_selectors': '',
  'custom_scripts': ''
};

(function ($, window) {
  var _delay = parseInt(wcapf_params.filter_input_delay);

  var delay = _delay >= 0 ? _delay : 300;

  var _keywordFilterDelay = parseInt(wcapf_params.keyword_filter_delay);

  var keywordFilterDelay = _keywordFilterDelay >= 0 ? _keywordFilterDelay : 100;
  var isPro = wcapf_params.wcapf_pro;
  var $body = $('body');
  var $document = $(document);
  var instanceIds = [];
  var defaultOrderByElement = wcapf_params.orderby_form + ' ' + wcapf_params.orderby_element;
  $('.wcapf-filter').each(function () {
    var id = $(this).data('id');

    if (!id) {
      return;
    }

    instanceIds.push(id);
  });
  window.tippyInstances = [];
  window.WCAPF = window.WCAPF || {};
  window.WCAPF = {
    handleFilterAccordion: function handleFilterAccordion() {
      var toggleAccordion = function toggleAccordion($el) {
        // Check to see if the accordion is opened
        var pressed = $el.attr('aria-expanded') === 'true'; // Change aria-expanded to the opposite state

        $el.attr('aria-expanded', !pressed);
        var $filterInner = $el.closest('.wcapf-filter').children('.wcapf-filter-inner');

        if (wcapf_params.enable_animation_for_filter_accordion) {
          $filterInner.slideToggle(wcapf_params.filter_accordion_animation_speed, wcapf_params.filter_accordion_animation_easing);
        } else {
          $filterInner.toggle();
        }
      };

      $body.on('click', '.wcapf-filter-accordion-trigger', function (e) {
        e.stopPropagation();
        toggleAccordion($(this));
      });
      $body.on('click', '.wcapf-filter-title.has-accordion', function () {
        var $trigger = $(this).find('.wcapf-filter-accordion-trigger');
        toggleAccordion($trigger);
      });
    },
    handleHierarchyToggle: function handleHierarchyToggle() {
      var toggleAccordion = function toggleAccordion($el) {
        // Check to see if the button is pressed
        var pressed = $el.attr('aria-pressed') === 'true'; // Change aria-pressed to the opposite state

        $el.attr('aria-pressed', !pressed);
        var $child = $el.closest('li').children('ul');

        if (wcapf_params.enable_animation_for_hierarchy_accordion) {
          $child.slideToggle(wcapf_params.hierarchy_accordion_animation_speed, wcapf_params.hierarchy_accordion_animation_easing);
        } else {
          $child.toggle();
        }
      };

      $body.on('click', '.wcapf-hierarchy-accordion-toggle', function () {
        toggleAccordion($(this));
      }).on('keydown', '.wcapf-hierarchy-accordion-toggle', function (e) {
        if (e.key === ' ' || e.key === 'Enter' || e.key === 'Spacebar') {
          // Prevent the default action to stop scrolling when space is pressed
          e.preventDefault();
          toggleAccordion($(this));
        }
      });
    },
    handleSoftLimit: function handleSoftLimit() {
      var toggleFilterOptions = function toggleFilterOptions($el) {
        // Check to see if the button is pressed
        var pressed = $el.attr('aria-pressed') === 'true'; // Change aria-pressed to the opposite state

        $el.attr('aria-pressed', !pressed);
        var $listWrapper = $el.closest('.wcapf-list-wrapper');

        if (pressed) {
          $listWrapper.removeClass('show-hidden-options');
        } else {
          $listWrapper.addClass('show-hidden-options');
        }
      };

      $body.on('click', '.wcapf-soft-limit-trigger', function () {
        toggleFilterOptions($(this));
      }).on('keydown', '.wcapf-soft-limit-trigger', function (e) {
        if (e.key === ' ' || e.key === 'Enter' || e.key === 'Spacebar') {
          // Prevent the default action to stop scrolling when space is pressed
          e.preventDefault();
          toggleFilterOptions($(this));
        }
      });
    },
    handleSearchFilterOptions: function handleSearchFilterOptions() {
      $body.on('input', '.wcapf-search-box input[type="text"]', function () {
        var $that = $(this);
        var $inner = $that.closest('.wcapf-filter-inner');
        var $filter = $inner.closest('.wcapf-filter');
        var softLimitEnabled = $filter.hasClass('has-soft-limit');
        var softLimitToggle = $filter.find('.wcapf-soft-limit-wrapper');
        var noResults = $filter.find('.wcapf-no-results-text');
        var visibleOptions = parseInt($filter.attr('data-visible-options'));
        var keyword = $that.val();

        if (!keyword.length) {
          var _index = 0;
          $filter.removeClass('search-active');
          $.each($inner.find('.wcapf-filter-options > li'), function () {
            _index++;
            var $filterItem = $(this);
            $filterItem.removeClass('keyword-matched');

            if (softLimitEnabled) {
              if (_index > visibleOptions) {
                $filterItem.addClass('wcapf-filter-option-hidden');
              } else {
                $filterItem.removeClass('wcapf-filter-option-hidden');
              }
            }
          });

          if (softLimitEnabled) {
            softLimitToggle.removeAttr('style');
          }

          noResults.children('span').text('');
          noResults.hide();
          return;
        }

        var index = 0;
        $filter.addClass('search-active');
        $.each($inner.find('.wcapf-filter-options > li'), function () {
          var $filterItem = $(this);
          var label = $filterItem.find('.wcapf-filter-item-label').data('label');

          if (label.toString().toLowerCase().includes(keyword.toLowerCase())) {
            index++;
            $filterItem.addClass('keyword-matched');

            if (softLimitEnabled) {
              if (index > visibleOptions) {
                $filterItem.addClass('wcapf-filter-option-hidden');
              } else {
                $filterItem.removeClass('wcapf-filter-option-hidden');
              }
            }
          } else {
            $filterItem.removeClass('keyword-matched');
          }
        });

        if (softLimitEnabled) {
          if (index <= visibleOptions) {
            softLimitToggle.hide();
          } else {
            softLimitToggle.show();
          }
        }

        if (0 === index) {
          noResults.children('span').text(keyword);
          noResults.show();
        } else {
          noResults.children('span').text('');
          noResults.hide();
        }
      });
      $body.on('click', '.wcapf-search-box .wcapf-clear-state', function () {
        var $that = $(this);
        var $searchBox = $that.closest('.wcapf-search-box');
        var $input = $searchBox.find('input[type="text"]');
        var $filter = $searchBox.closest('.wcapf-filter');
        $input.val('');

        if ($filter.hasClass('wcapf-filter-keyword')) {
          $input.trigger('change');
        } else {
          $input.trigger('input');
        }
      });
      $body.on('change', '.wcapf-filter-keyword input[type="text"]', function () {
        var $that = $(this);
        var $wrapper = $that.closest('.wcapf-keyword-filter-wrapper');
        var keyword = $that.val(); // Clear any previously set timer before setting a fresh one

        clearTimeout($that.data('timer'));
        var filterURL = $wrapper.data('filter-url');
        var clearFilterURL = $wrapper.data('clear-filter-url');
        var url;

        if (keyword.length) {
          url = filterURL.replace('%s', keyword);
        } else {
          url = clearFilterURL;
        }

        $that.data('timer', setTimeout(function () {
          WCAPF.requestFilter(url);
        }, keywordFilterDelay));
      });
    },
    updateProductsCountResult: function updateProductsCountResult($response) {
      var $container = $(wcapf_params.shop_loop_container);
      var selector = '.woocommerce-result-count';
      var newCount = $response.find(selector).html();
      $body.find(selector).each(function () {
        var $el = $(this);

        if (!$container.has($el).length) {
          $el.html(newCount);
        }
      });
    },
    scrollTo: function scrollTo() {
      if ('none' === wcapf_params.scroll_window) {
        return;
      }

      var scrollFor = wcapf_params.scroll_window_for;
      var isMobile = wcapf_params.is_mobile;
      var proceed = false;

      if ('mobile' === scrollFor && isMobile) {
        proceed = true;
      } else if ('desktop' === scrollFor && !isMobile) {
        proceed = true;
      } else if ('both' === scrollFor) {
        proceed = true;
      }

      if (!proceed) {
        return;
      }

      var adjustingOffset = 0,
          offset = 0;

      if (wcapf_params.scroll_to_top_offset) {
        adjustingOffset = parseInt(wcapf_params.scroll_to_top_offset);
      }

      var container;

      if ($(wcapf_params.shop_loop_container).length) {
        container = wcapf_params.shop_loop_container;
      } else if ($(wcapf_params.not_found_container).length) {
        container = wcapf_params.not_found_container;
      }

      if ('custom' === wcapf_params.scroll_window) {
        container = wcapf_params.scroll_window_custom_element;
      }

      var $container = $(container);

      if ($container.length) {
        offset = $container.offset().top - adjustingOffset;

        if (offset < 0) {
          offset = 0;
        }

        $('html, body').stop().animate({
          scrollTop: offset
        }, wcapf_params.scroll_to_top_speed, wcapf_params.scroll_to_top_easing);
      }
    },
    // Things are done before fetching the products like showing the loading indicator.
    beforeFetchingProducts: function beforeFetchingProducts(triggeredBy) {
      $body.find('.wcapf-loader').addClass('is-active');

      if (!isPro && 'immediately' === wcapf_params.scroll_window_when) {
        WCAPF.scrollTo();
      }

      $document.trigger('wcapf_before_fetching_products', [triggeredBy]);
    },
    destroyTippyInstances: function destroyTippyInstances() {
      if (wcapf_params.use_tippyjs) {
        // @source https://github.com/atomiks/tippyjs/issues/473
        tippyInstances.forEach(function (instance) {
          instance.destroy();
        });
        tippyInstances.length = 0; // clear it
      }
    },
    // Things are done before updating the products like hiding the loading indicator.
    beforeUpdatingProducts: function beforeUpdatingProducts($response, triggeredBy) {
      $body.find('.wcapf-loader').removeClass('is-active'); // Maybe good for performance.

      WCAPF.destroyTippyInstances();
      $document.trigger('wcapf_before_updating_products', [$response, triggeredBy]);
    },
    afterUpdatingProducts: function afterUpdatingProducts($response, triggeredBy) {
      WCAPF.updateProductsCountResult($response); // Reinitialize wcapf.

      WCAPF.init();

      if (!isPro && 'after' === wcapf_params.scroll_window_when) {
        WCAPF.scrollTo();
      } // Trigger events.


      $(document).trigger('ready');
      $(window).trigger('scroll');
      $(window).trigger('resize'); // A3 Lazy Load support.

      $(window).trigger('lazyshow');

      if (wcapf_params.custom_scripts) {
        eval(wcapf_params.custom_scripts);
      }

      $document.trigger('wcapf_after_updating_products', [$response, triggeredBy]);
    },
    filterProducts: function filterProducts() {
      var triggeredBy = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'filter';
      WCAPF.beforeFetchingProducts(triggeredBy);
      $.ajax({
        url: window.location.href,
        success: function success(response) {
          var $response = $(response);
          WCAPF.beforeUpdatingProducts($response, triggeredBy);
          /**
           * Update document title.
           *
           * @source https://stackoverflow.com/a/7599562
           */

          if (wcapf_params.update_document_title) {
            document.title = $response.filter('title').text();
          } // Update the instances.


          var _iterator = _createForOfIteratorHelper(instanceIds),
              _step;

          try {
            var _loop = function _loop() {
              var id = _step.value;
              var instanceId = '[data-id="' + id + '"]';
              var $instance = $(instanceId);
              var $inner = $instance.find('.wcapf-filter-inner');

              var _instance = $response.find(instanceId); // Preserve hierarchy accordion state.


              if (wcapf_params.preserve_hierarchy_accordion_state) {
                if ($instance.hasClass('has-hierarchy-accordion')) {
                  $instance.find('.wcapf-hierarchy-accordion-toggle').each(function () {
                    var $el = $(this);
                    var id = $el.data('id');
                    var toggleSelector = ".wcapf-hierarchy-accordion-toggle[data-id=\"".concat(id, "\"]"); // Check to see if the accordion is opened

                    var pressed = $el.attr('aria-pressed') === 'true';

                    if (pressed) {
                      _instance.find(toggleSelector).attr('aria-pressed', 'true');

                      _instance.find(toggleSelector).closest('li').children('ul').show();
                    } else {
                      _instance.find(toggleSelector).attr('aria-pressed', 'false');

                      _instance.find(toggleSelector).closest('li').children('ul').hide();
                    }
                  });
                }
              } // Preserve soft limit state.


              if (wcapf_params.preserve_soft_limit_state) {
                if ($instance.hasClass('has-soft-limit')) {
                  var $listWrapper = $instance.find('.wcapf-list-wrapper');

                  if ($listWrapper.hasClass('show-hidden-options')) {
                    _instance.find('.wcapf-list-wrapper').addClass('show-hidden-options');

                    _instance.find('.wcapf-soft-limit-trigger').attr('aria-pressed', 'true');
                  } else {
                    _instance.find('.wcapf-list-wrapper').removeClass('show-hidden-options');

                    _instance.find('.wcapf-soft-limit-trigger').attr('aria-pressed', 'false');
                  }
                }
              }

              var _html = _instance.find('.wcapf-filter-inner').html(); // Finally update the instance.


              $inner.html(_html);
              $instance.trigger('wcapf-filter-updated', [_instance]);
            };

            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              _loop();
            } // Update the active filters and reset filters.

          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }

          $body.find('.wcapf-active-filters, .wcapf-reset-filters').each(function () {
            var $that = $(this);
            var instanceId = '[data-id="' + $that.data('id') + '"]';
            $that.html($response.find(instanceId).html());
          }); // Replace old shop loop with new one.

          var $shopLoopContainer = $response.find(wcapf_params.shop_loop_container);
          var $notFoundContainer = $response.find(wcapf_params.not_found_container);

          if (wcapf_params.shop_loop_container === wcapf_params.not_found_container) {
            $(wcapf_params.shop_loop_container).html($shopLoopContainer.html());
          } else {
            if ($(wcapf_params.not_found_container).length) {
              if ($shopLoopContainer.length) {
                $(wcapf_params.not_found_container).html($shopLoopContainer.html());
              } else if ($notFoundContainer.length) {
                $(wcapf_params.not_found_container).html($notFoundContainer.html());
              }
            } else if ($(wcapf_params.shop_loop_container).length) {
              if ($shopLoopContainer.length) {
                $(wcapf_params.shop_loop_container).html($shopLoopContainer.html());
              } else if ($notFoundContainer.length) {
                $(wcapf_params.shop_loop_container).html($notFoundContainer.html());
              }
            }
          }

          WCAPF.afterUpdatingProducts($response, triggeredBy);
        }
      });
    },
    requestFilter: function requestFilter(url) {
      var triggeredBy = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'filter';

      if (!url) {
        return;
      }

      if (wcapf_params.disable_ajax) {
        window.location.href = url;
      } else {
        history.pushState({
          wcapf: true
        }, '', url);
        WCAPF.filterProducts(triggeredBy);
      }
    },
    handleNumberInputFilters: function handleNumberInputFilters() {
      var rangeNumberSelectors = '.wcapf-range-number .min-value, .wcapf-range-number .max-value';
      $body.on('input', rangeNumberSelectors, function () {
        var $item = $(this);
        var $rangeNumber = $item.closest('.wcapf-range-number');
        var formatNumbers = $rangeNumber.attr('data-format-numbers');
        var rangeMinValue = parseFloat($rangeNumber.attr('data-range-min-value'));
        var rangeMaxValue = parseFloat($rangeNumber.attr('data-range-max-value'));
        var oldMinValue = parseFloat($rangeNumber.attr('data-min-value'));
        var oldMaxValue = parseFloat($rangeNumber.attr('data-max-value'));
        var decimalPlaces = $rangeNumber.attr('data-decimal-places');
        var thousandSeparator = $rangeNumber.attr('data-thousand-separator');
        var decimalSeparator = $rangeNumber.attr('data-decimal-separator'); // Clear any previously set timer before setting a fresh one

        clearTimeout($item.data('timer'));

        var getValue = function getValue(floatValue) {
          if (formatNumbers) {
            return numberFormat(floatValue, decimalPlaces, decimalSeparator, thousandSeparator);
          }

          return floatValue;
        };

        $item.data('timer', setTimeout(function () {
          $item.removeData('timer');
          var minValue = parseFloat($rangeNumber.find('.min-value').val());
          var maxValue = parseFloat($rangeNumber.find('.max-value').val()); // Force the minValue not to be empty.

          if (isNaN(minValue)) {
            minValue = rangeMinValue;
            $rangeNumber.find('.min-value').val(getValue(minValue));
          } else {
            $rangeNumber.find('.min-value').val(getValue(minValue));
          } // Force the maxValue not to be empty.


          if (isNaN(maxValue)) {
            maxValue = rangeMaxValue;
            $rangeNumber.find('.max-value').val(getValue(maxValue));
          } else {
            $rangeNumber.find('.max-value').val(getValue(maxValue));
          } // Force the minValue not to go below the rangeMinValue.


          if (minValue < rangeMinValue) {
            minValue = rangeMinValue;
            $rangeNumber.find('.min-value').val(getValue(minValue));
          } // Force the minValue not to go up the rangeMaxValue.


          if (minValue > rangeMaxValue) {
            minValue = rangeMaxValue;
            $rangeNumber.find('.min-value').val(getValue(minValue));
          } // Force the maxValue not to go up the rangeMaxValue.


          if (maxValue > rangeMaxValue) {
            maxValue = rangeMaxValue;
            $rangeNumber.find('.max-value').val(getValue(maxValue));
          } // Force the maxValue not to go below the minValue.


          if (minValue > maxValue) {
            maxValue = minValue;
            $rangeNumber.find('.max-value').val(getValue(maxValue));
          } // If value is not changed then don't proceed.


          if (minValue === oldMinValue && maxValue === oldMaxValue) {
            return;
          }

          if (minValue === rangeMinValue && maxValue === rangeMaxValue) {
            // Remove range filter.
            WCAPF.requestFilter($rangeNumber.data('clear-filter-url'));
          } else {
            // Add range filter.
            var url = $rangeNumber.data('url').replace('%1s', minValue).replace('%2s', maxValue);
            WCAPF.requestFilter(url);
          }
        }, delay));
      });
    },
    handleDateInputFilters: function handleDateInputFilters() {
      $body.on('change', '.wcapf-date-input .date-input', function () {
        var $filter = $(this).closest('.wcapf-date-input');
        var isRange = $filter.data('is-range');
        var filterUrl = ''; // Clear any previously set timer before setting a fresh one

        clearTimeout($filter.data('timer'));

        if (isRange) {
          var from = $filter.find('.date-from-input').val();
          var to = $filter.find('.date-to-input').val();

          if (from && to) {
            filterUrl = $filter.data('url').replace('%1s', from).replace('%2s', to);
          } else if (!from && !to) {
            filterUrl = $filter.data('clear-filter-url');
          }
        } else {
          var _from = $filter.find('.date-from-input').val();

          if (_from) {
            filterUrl = $filter.data('url').replace('%s', _from);
          } else {
            filterUrl = $filter.data('clear-filter-url');
          }
        }

        if (filterUrl) {
          $filter.data('timer', setTimeout(function () {
            $filter.removeData('timer');
            WCAPF.requestFilter(filterUrl);
          }, delay));
        }
      });
    },
    handleListFilters: function handleListFilters() {
      var nativeInputs = '.list-type-native [type="checkbox"],' + '.list-type-native [type="radio"],' + '.list-type-custom-checkbox [type="checkbox"]';
      $body.on('change', nativeInputs, function () {
        $(this).closest('.wcapf-filter-item').toggleClass('item-active');
        WCAPF.requestFilter($(this).data('url'));
      });
      var customRadioSelector = '.list-type-custom-radio';
      $body.on('change', customRadioSelector + ' [type="checkbox"]', function () {
        $(this).closest('.wcapf-filter-item').toggleClass('item-active'); // https://stackoverflow.com/a/5839924

        $(this).closest(customRadioSelector).find('.wcapf-filter-item.item-active [type="checkbox"]').not(this).prop('checked', false).closest('.wcapf-filter-item').removeClass('item-active');
        WCAPF.requestFilter($(this).data('url'));
      });
    },
    handleDropdownFilters: function handleDropdownFilters() {
      $body.on('change', '.wcapf-dropdown-wrapper select', function () {
        var $select = $(this);
        var values = $select.val();
        var filterURL = $select.data('url');
        var clearFilterURL = $select.data('clear-filter-url');
        var url;

        if (values.length) {
          url = filterURL.replace('%s', values.toString());
        } else {
          url = clearFilterURL;
        }

        WCAPF.requestFilter(url);
      });
    },
    handlePagination: function handlePagination() {
      if (wcapf_params.enable_pagination_via_ajax && wcapf_params.pagination_container) {
        var $container = $(wcapf_params.shop_loop_container);

        var _selectors = wcapf_params.pagination_container.split(',');

        var selectors = [];

        _selectors.forEach(function (selector) {
          if (selector) {
            selectors.push(selector + ' a');
          }
        });

        var selector = selectors.join(',');

        if ($container.length) {
          $container.on('click', selector, function (e) {
            e.preventDefault();
            var href = $(this).attr('href');
            WCAPF.requestFilter(href, 'paginate');
          });
        }
      }
    },
    handleDefaultOrderby: function handleDefaultOrderby() {
      if (!wcapf_params.sorting_control) {
        // Submit the orderby form when value is changed.
        $body.on('change', defaultOrderByElement, function () {
          $(this).closest('form').trigger('submit');
        });
        return;
      } // Prevent the auto submission of the orderby form.


      $body.on('submit', wcapf_params.orderby_form, function () {
        return false;
      }); // Handle the filter request via ajax when the orderby value is changed.

      $body.on('change', defaultOrderByElement, function () {
        var order = $(this).val();
        var url = new URL(window.location);
        url.searchParams.set('orderby', order);
        WCAPF.requestFilter(getOrderByUrl(url.href));
        return false;
      });
    },
    handleClearFilter: function handleClearFilter() {
      $body.on('click', '.wcapf-filter-clear-btn', function (e) {
        e.stopPropagation();
        WCAPF.requestFilter($(this).attr('data-clear-filter-url'));
      });
    },
    handleFilterTooltip: function handleFilterTooltip() {
      // noinspection JSUnresolvedReference
      if ('function' !== typeof tippy) {
        return;
      }

      if (!wcapf_params.use_tippyjs) {
        return;
      } // noinspection JSUnresolvedReference


      tippy('.wcapf-filter-tooltip', {
        placement: 'top',
        content: function content(reference) {
          return reference.getAttribute('data-content');
        },
        allowHTML: true
      });
    },
    initCombobox: function initCombobox() {
      if (!jQuery().chosenWCAPF) {
        return;
      }

      var templateResult = function templateResult(text, data) {
        return ['<span>' + text + '</span>', '<span class="wcapf-count">' + data['countMarkup'] + '</span>'].join('');
      };

      var templateSelection = function templateSelection(text, data) {
        return ['<span class="wcapf-count-' + data.count + '">' + text + '</span>', '<span class="wcapf-count wcapf-count-' + data.count + '">' + data['countMarkup'] + '</span>'].join('');
      };

      var defaults = {
        inherit_select_classes: true,
        inherit_option_classes: true,
        no_results_text: wcapf_params.combobox_no_results_text,
        options_none_text: wcapf_params.combobox_options_none_text,
        search_contains: true,
        // Match from anywhere in string.
        search_in_values: true // Search in values also.

      };

      if (wcapf_params.is_rtl) {
        defaults['rtl'] = true;
      }

      $body.find('.wcapf-chosen').each(function () {
        var $this = $(this);

        var options = _objectSpread({}, defaults); // If hierarchy enabled then we show the selected options.


        if ($this.hasClass('has-hierarchy')) {
          options['display_selected_options'] = true;
        } else {
          options['display_selected_options'] = wcapf_params.combobox_display_selected_options;
        } // Enable templating when showing count.


        if ($this.hasClass('with-count')) {
          options['templateResult'] = templateResult;
          options['templateSelection'] = templateSelection;
        } // Disable search box.


        if (!$this.data('enable-search')) {
          options['disable_search'] = true;
        }

        $this.chosenWCAPF(options);
      }); // Attach chosen for default orderby.

      if (wcapf_params.attach_combobox_on_sorting) {
        var disableSearch = true;

        if (wcapf_params.search_box_in_default_orderby) {
          disableSearch = false;
        }

        var options = _objectSpread({}, defaults);

        options['disable_search'] = disableSearch;
        $body.find(defaultOrderByElement).chosenWCAPF(options);
      }
    },
    initRangeSlider: function initRangeSlider() {
      if ('undefined' === typeof noUiSlider) {
        return;
      }

      $body.find('.wcapf-range-slider').each(function () {
        var $item = $(this);
        var $slider = $item.find('.wcapf-noui-slider');
        var sliderId = $slider.attr('id');
        var displayValuesAs = $item.attr('data-display-values-as');
        var formatNumbers = $item.attr('data-format-numbers');
        var rangeMinValue = parseFloat($item.attr('data-range-min-value'));
        var rangeMaxValue = parseFloat($item.attr('data-range-max-value'));
        var step = parseFloat($item.attr('data-step'));
        var decimalPlaces = $item.attr('data-decimal-places');
        var thousandSeparator = $item.attr('data-thousand-separator');
        var decimalSeparator = $item.attr('data-decimal-separator');
        var minValue = parseFloat($item.attr('data-min-value'));
        var maxValue = parseFloat($item.attr('data-max-value'));
        var $minValue = $item.find('.min-value');
        var $maxValue = $item.find('.max-value');
        var slider = document.getElementById(sliderId);
        noUiSlider.create(slider, {
          start: [minValue, maxValue],
          step: step,
          connect: true,
          cssPrefix: 'wcapf-noui-',
          range: {
            'min': rangeMinValue,
            'max': rangeMaxValue
          }
        });
        slider.noUiSlider.on('update', function (values) {
          var minValue;
          var maxValue;

          if (formatNumbers) {
            minValue = numberFormat(values[0], decimalPlaces, decimalSeparator, thousandSeparator);
            maxValue = numberFormat(values[1], decimalPlaces, decimalSeparator, thousandSeparator);
          } else {
            minValue = parseFloat(values[0]);
            maxValue = parseFloat(values[1]);
          }

          if ('plain_text' === displayValuesAs) {
            $minValue.html(minValue);
            $maxValue.html(maxValue);
          } else {
            $minValue.val(minValue);
            $maxValue.val(maxValue);
          }
        });

        function filterProductsAccordingToSlider(values) {
          var _minValue = parseFloat(values[0]);

          var _maxValue = parseFloat(values[1]); // If value is not changed then don't proceed.


          if (_minValue === minValue && _maxValue === maxValue) {
            return;
          }

          if (_minValue === rangeMinValue && _maxValue === rangeMaxValue) {
            // Remove range filter.
            WCAPF.requestFilter($item.data('clear-filter-url'));
          } else {
            // Add range filter.
            var url = $item.data('url').replace('%1s', _minValue).replace('%2s', _maxValue);
            WCAPF.requestFilter(url);
          }
        }

        slider.noUiSlider.on('change', function (values) {
          // Clear any previously set timer before setting a fresh one
          clearTimeout($item.data('timer'));
          $item.data('timer', setTimeout(function () {
            $item.removeData('timer');
            filterProductsAccordingToSlider(values);
          }, delay));
        });
        $minValue.on('input', function () {
          var $input = $(this); // Clear any previously set timer before setting a fresh one

          clearTimeout($input.data('timer'));
          $input.data('timer', setTimeout(function () {
            $input.removeData('timer');
            var minValue = $input.val();
            slider.noUiSlider.set([minValue, null]);
            filterProductsAccordingToSlider(slider.noUiSlider.get());
          }, delay));
        });
        $maxValue.on('input', function () {
          var $input = $(this); // Clear any previously set timer before setting a fresh one

          clearTimeout($input.data('timer'));
          $input.data('timer', setTimeout(function () {
            $input.removeData('timer');
            var maxValue = $input.val();
            slider.noUiSlider.set([null, maxValue]);
            filterProductsAccordingToSlider(slider.noUiSlider.get());
          }, delay));
        });
      });
    },
    initDatepicker: function initDatepicker() {
      if (!jQuery().datepicker) {
        return;
      }

      var $wcapfDateFilter = $body.find('.wcapf-date-input');
      var format = $wcapfDateFilter.attr('data-date-format');
      var yearDropdown = $wcapfDateFilter.attr('data-date-picker-year-dropdown');
      var monthDropdown = $wcapfDateFilter.attr('data-date-picker-month-dropdown');
      var $from = $wcapfDateFilter.find('.date-from-input');
      var $to = $wcapfDateFilter.find('.date-to-input');
      $from.datepicker({
        dateFormat: format,
        changeYear: yearDropdown,
        changeMonth: monthDropdown
      });
      $to.datepicker({
        dateFormat: format,
        changeYear: yearDropdown,
        changeMonth: monthDropdown
      });
    },
    initFilterOptionTooltip: function initFilterOptionTooltip() {
      // noinspection JSUnresolvedReference
      if ('function' !== typeof tippy) {
        return;
      }

      if (!wcapf_params.use_tippyjs) {
        return;
      }

      var tooltipPositions = ['top', 'right', 'bottom', 'left'];
      tooltipPositions.forEach(function (tooltipPosition) {
        var identifier = 'data-wcapf-tooltip-' + tooltipPosition; // noinspection JSUnresolvedReference

        var instances = tippy('[' + identifier + ']', {
          placement: tooltipPosition,
          content: function content(reference) {
            return reference.getAttribute(identifier);
          },
          allowHTML: true
        });
        window.tippyInstances = tippyInstances.concat(instances);
      });
    },
    init: function init() {
      WCAPF.initCombobox();
      WCAPF.initRangeSlider();
      WCAPF.initDatepicker();
      WCAPF.initFilterOptionTooltip();
    },
    initPopState: function initPopState() {
      if (wcapf_params.reload_on_back && wcapf_params.found_wcapf) {
        history.replaceState({
          wcapf: true
        }, '', window.location); // Handle the popstate event(browser's back/forward)

        window.addEventListener('popstate', function (e) {
          if (null !== e.state && e.state.hasOwnProperty('wcapf')) {
            WCAPF.filterProducts('popstate');
          }
        });
      }
    }
  };
  /**
   * Enable it if necessary.
   *
   * @source https://stackoverflow.com/a/33004917
   */

  if ('scrollRestoration' in history) {// history.scrollRestoration = 'manual';
  }
})(jQuery, window);

(function ($, WCAPF) {
  WCAPF.init();
  WCAPF.initPopState();
  WCAPF.handleFilterAccordion();
  WCAPF.handleHierarchyToggle();
  WCAPF.handleSoftLimit();
  WCAPF.handleSearchFilterOptions();
  WCAPF.handleListFilters();
  WCAPF.handleDropdownFilters();
  WCAPF.handleNumberInputFilters();
  WCAPF.handleDateInputFilters();
  WCAPF.handlePagination();
  WCAPF.handleDefaultOrderby();
  WCAPF.handleClearFilter();
  WCAPF.handleFilterTooltip();
  /**
   * Make it compatible with other plugins.
   */

  $(document).on('wcapf_after_updating_products', function () {
    // woo-variation-swatches
    $(document).trigger('woo_variation_swatches_pro_init');
  });
})(jQuery, window.WCAPF);
"use strict";

/**
 * @source https://stackoverflow.com/a/34141813
 *
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 *
 * @returns {string}
 */
function numberFormat(number, decimals, dec_point, thousands_sep) {
  // Strip all characters but numerical ones.
  number = (number + '').replace(/[^\d+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number;
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
  var sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep;
  var dec = typeof dec_point === 'undefined' ? '.' : dec_point;
  var s;

  var toFixedFix = function toFixedFix(n, prec) {
    var k = Math.pow(10, prec);
    return '' + Math.round(n * k) / k;
  }; // Fix for IE parseFloat(0.55).toFixed(0) = 0;


  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }

  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }

  return s.join(dec);
}

function cleanUrl(url) {
  return url.replace(/%2C/g, ',');
}

function getOrderByUrl(url) {
  var paged = parseInt(url.replace(/.+\/page\/(\d+)+/, '$1'));

  if (paged) {
    url = url.replace(/page\/(\d+)\//, '');
  }

  return cleanUrl(url);
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiLCJ1dGlscy5qcyJdLCJuYW1lcyI6WyJ3Y2FwZl9wYXJhbXMiLCIkIiwid2luZG93IiwiX2RlbGF5IiwicGFyc2VJbnQiLCJmaWx0ZXJfaW5wdXRfZGVsYXkiLCJkZWxheSIsIl9rZXl3b3JkRmlsdGVyRGVsYXkiLCJrZXl3b3JkX2ZpbHRlcl9kZWxheSIsImtleXdvcmRGaWx0ZXJEZWxheSIsImlzUHJvIiwid2NhcGZfcHJvIiwiJGJvZHkiLCIkZG9jdW1lbnQiLCJkb2N1bWVudCIsImluc3RhbmNlSWRzIiwiZGVmYXVsdE9yZGVyQnlFbGVtZW50Iiwib3JkZXJieV9mb3JtIiwib3JkZXJieV9lbGVtZW50IiwiZWFjaCIsImlkIiwiZGF0YSIsInB1c2giLCJ0aXBweUluc3RhbmNlcyIsIldDQVBGIiwiaGFuZGxlRmlsdGVyQWNjb3JkaW9uIiwidG9nZ2xlQWNjb3JkaW9uIiwiJGVsIiwicHJlc3NlZCIsImF0dHIiLCIkZmlsdGVySW5uZXIiLCJjbG9zZXN0IiwiY2hpbGRyZW4iLCJlbmFibGVfYW5pbWF0aW9uX2Zvcl9maWx0ZXJfYWNjb3JkaW9uIiwic2xpZGVUb2dnbGUiLCJmaWx0ZXJfYWNjb3JkaW9uX2FuaW1hdGlvbl9zcGVlZCIsImZpbHRlcl9hY2NvcmRpb25fYW5pbWF0aW9uX2Vhc2luZyIsInRvZ2dsZSIsIm9uIiwiZSIsInN0b3BQcm9wYWdhdGlvbiIsIiR0cmlnZ2VyIiwiZmluZCIsImhhbmRsZUhpZXJhcmNoeVRvZ2dsZSIsIiRjaGlsZCIsImVuYWJsZV9hbmltYXRpb25fZm9yX2hpZXJhcmNoeV9hY2NvcmRpb24iLCJoaWVyYXJjaHlfYWNjb3JkaW9uX2FuaW1hdGlvbl9zcGVlZCIsImhpZXJhcmNoeV9hY2NvcmRpb25fYW5pbWF0aW9uX2Vhc2luZyIsImtleSIsInByZXZlbnREZWZhdWx0IiwiaGFuZGxlU29mdExpbWl0IiwidG9nZ2xlRmlsdGVyT3B0aW9ucyIsIiRsaXN0V3JhcHBlciIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJoYW5kbGVTZWFyY2hGaWx0ZXJPcHRpb25zIiwiJHRoYXQiLCIkaW5uZXIiLCIkZmlsdGVyIiwic29mdExpbWl0RW5hYmxlZCIsImhhc0NsYXNzIiwic29mdExpbWl0VG9nZ2xlIiwibm9SZXN1bHRzIiwidmlzaWJsZU9wdGlvbnMiLCJrZXl3b3JkIiwidmFsIiwibGVuZ3RoIiwiaW5kZXgiLCIkZmlsdGVySXRlbSIsInJlbW92ZUF0dHIiLCJ0ZXh0IiwiaGlkZSIsImxhYmVsIiwidG9TdHJpbmciLCJ0b0xvd2VyQ2FzZSIsImluY2x1ZGVzIiwic2hvdyIsIiRzZWFyY2hCb3giLCIkaW5wdXQiLCJ0cmlnZ2VyIiwiJHdyYXBwZXIiLCJjbGVhclRpbWVvdXQiLCJmaWx0ZXJVUkwiLCJjbGVhckZpbHRlclVSTCIsInVybCIsInJlcGxhY2UiLCJzZXRUaW1lb3V0IiwicmVxdWVzdEZpbHRlciIsInVwZGF0ZVByb2R1Y3RzQ291bnRSZXN1bHQiLCIkcmVzcG9uc2UiLCIkY29udGFpbmVyIiwic2hvcF9sb29wX2NvbnRhaW5lciIsInNlbGVjdG9yIiwibmV3Q291bnQiLCJodG1sIiwiaGFzIiwic2Nyb2xsVG8iLCJzY3JvbGxfd2luZG93Iiwic2Nyb2xsRm9yIiwic2Nyb2xsX3dpbmRvd19mb3IiLCJpc01vYmlsZSIsImlzX21vYmlsZSIsInByb2NlZWQiLCJhZGp1c3RpbmdPZmZzZXQiLCJvZmZzZXQiLCJzY3JvbGxfdG9fdG9wX29mZnNldCIsImNvbnRhaW5lciIsIm5vdF9mb3VuZF9jb250YWluZXIiLCJzY3JvbGxfd2luZG93X2N1c3RvbV9lbGVtZW50IiwidG9wIiwic3RvcCIsImFuaW1hdGUiLCJzY3JvbGxUb3AiLCJzY3JvbGxfdG9fdG9wX3NwZWVkIiwic2Nyb2xsX3RvX3RvcF9lYXNpbmciLCJiZWZvcmVGZXRjaGluZ1Byb2R1Y3RzIiwidHJpZ2dlcmVkQnkiLCJzY3JvbGxfd2luZG93X3doZW4iLCJkZXN0cm95VGlwcHlJbnN0YW5jZXMiLCJ1c2VfdGlwcHlqcyIsImZvckVhY2giLCJpbnN0YW5jZSIsImRlc3Ryb3kiLCJiZWZvcmVVcGRhdGluZ1Byb2R1Y3RzIiwiYWZ0ZXJVcGRhdGluZ1Byb2R1Y3RzIiwiaW5pdCIsImN1c3RvbV9zY3JpcHRzIiwiZXZhbCIsImZpbHRlclByb2R1Y3RzIiwiYWpheCIsImxvY2F0aW9uIiwiaHJlZiIsInN1Y2Nlc3MiLCJyZXNwb25zZSIsInVwZGF0ZV9kb2N1bWVudF90aXRsZSIsInRpdGxlIiwiZmlsdGVyIiwiaW5zdGFuY2VJZCIsIiRpbnN0YW5jZSIsIl9pbnN0YW5jZSIsInByZXNlcnZlX2hpZXJhcmNoeV9hY2NvcmRpb25fc3RhdGUiLCJ0b2dnbGVTZWxlY3RvciIsInByZXNlcnZlX3NvZnRfbGltaXRfc3RhdGUiLCJfaHRtbCIsIiRzaG9wTG9vcENvbnRhaW5lciIsIiRub3RGb3VuZENvbnRhaW5lciIsImRpc2FibGVfYWpheCIsImhpc3RvcnkiLCJwdXNoU3RhdGUiLCJ3Y2FwZiIsImhhbmRsZU51bWJlcklucHV0RmlsdGVycyIsInJhbmdlTnVtYmVyU2VsZWN0b3JzIiwiJGl0ZW0iLCIkcmFuZ2VOdW1iZXIiLCJmb3JtYXROdW1iZXJzIiwicmFuZ2VNaW5WYWx1ZSIsInBhcnNlRmxvYXQiLCJyYW5nZU1heFZhbHVlIiwib2xkTWluVmFsdWUiLCJvbGRNYXhWYWx1ZSIsImRlY2ltYWxQbGFjZXMiLCJ0aG91c2FuZFNlcGFyYXRvciIsImRlY2ltYWxTZXBhcmF0b3IiLCJnZXRWYWx1ZSIsImZsb2F0VmFsdWUiLCJudW1iZXJGb3JtYXQiLCJyZW1vdmVEYXRhIiwibWluVmFsdWUiLCJtYXhWYWx1ZSIsImlzTmFOIiwiaGFuZGxlRGF0ZUlucHV0RmlsdGVycyIsImlzUmFuZ2UiLCJmaWx0ZXJVcmwiLCJmcm9tIiwidG8iLCJoYW5kbGVMaXN0RmlsdGVycyIsIm5hdGl2ZUlucHV0cyIsInRvZ2dsZUNsYXNzIiwiY3VzdG9tUmFkaW9TZWxlY3RvciIsIm5vdCIsInByb3AiLCJoYW5kbGVEcm9wZG93bkZpbHRlcnMiLCIkc2VsZWN0IiwidmFsdWVzIiwiaGFuZGxlUGFnaW5hdGlvbiIsImVuYWJsZV9wYWdpbmF0aW9uX3ZpYV9hamF4IiwicGFnaW5hdGlvbl9jb250YWluZXIiLCJfc2VsZWN0b3JzIiwic3BsaXQiLCJzZWxlY3RvcnMiLCJqb2luIiwiaGFuZGxlRGVmYXVsdE9yZGVyYnkiLCJzb3J0aW5nX2NvbnRyb2wiLCJvcmRlciIsIlVSTCIsInNlYXJjaFBhcmFtcyIsInNldCIsImdldE9yZGVyQnlVcmwiLCJoYW5kbGVDbGVhckZpbHRlciIsImhhbmRsZUZpbHRlclRvb2x0aXAiLCJ0aXBweSIsInBsYWNlbWVudCIsImNvbnRlbnQiLCJyZWZlcmVuY2UiLCJnZXRBdHRyaWJ1dGUiLCJhbGxvd0hUTUwiLCJpbml0Q29tYm9ib3giLCJqUXVlcnkiLCJjaG9zZW5XQ0FQRiIsInRlbXBsYXRlUmVzdWx0IiwidGVtcGxhdGVTZWxlY3Rpb24iLCJjb3VudCIsImRlZmF1bHRzIiwiaW5oZXJpdF9zZWxlY3RfY2xhc3NlcyIsImluaGVyaXRfb3B0aW9uX2NsYXNzZXMiLCJub19yZXN1bHRzX3RleHQiLCJjb21ib2JveF9ub19yZXN1bHRzX3RleHQiLCJvcHRpb25zX25vbmVfdGV4dCIsImNvbWJvYm94X29wdGlvbnNfbm9uZV90ZXh0Iiwic2VhcmNoX2NvbnRhaW5zIiwic2VhcmNoX2luX3ZhbHVlcyIsImlzX3J0bCIsIiR0aGlzIiwib3B0aW9ucyIsImNvbWJvYm94X2Rpc3BsYXlfc2VsZWN0ZWRfb3B0aW9ucyIsImF0dGFjaF9jb21ib2JveF9vbl9zb3J0aW5nIiwiZGlzYWJsZVNlYXJjaCIsInNlYXJjaF9ib3hfaW5fZGVmYXVsdF9vcmRlcmJ5IiwiaW5pdFJhbmdlU2xpZGVyIiwibm9VaVNsaWRlciIsIiRzbGlkZXIiLCJzbGlkZXJJZCIsImRpc3BsYXlWYWx1ZXNBcyIsInN0ZXAiLCIkbWluVmFsdWUiLCIkbWF4VmFsdWUiLCJzbGlkZXIiLCJnZXRFbGVtZW50QnlJZCIsImNyZWF0ZSIsInN0YXJ0IiwiY29ubmVjdCIsImNzc1ByZWZpeCIsInJhbmdlIiwiZmlsdGVyUHJvZHVjdHNBY2NvcmRpbmdUb1NsaWRlciIsIl9taW5WYWx1ZSIsIl9tYXhWYWx1ZSIsImdldCIsImluaXREYXRlcGlja2VyIiwiZGF0ZXBpY2tlciIsIiR3Y2FwZkRhdGVGaWx0ZXIiLCJmb3JtYXQiLCJ5ZWFyRHJvcGRvd24iLCJtb250aERyb3Bkb3duIiwiJGZyb20iLCIkdG8iLCJkYXRlRm9ybWF0IiwiY2hhbmdlWWVhciIsImNoYW5nZU1vbnRoIiwiaW5pdEZpbHRlck9wdGlvblRvb2x0aXAiLCJ0b29sdGlwUG9zaXRpb25zIiwidG9vbHRpcFBvc2l0aW9uIiwiaWRlbnRpZmllciIsImluc3RhbmNlcyIsImNvbmNhdCIsImluaXRQb3BTdGF0ZSIsInJlbG9hZF9vbl9iYWNrIiwiZm91bmRfd2NhcGYiLCJyZXBsYWNlU3RhdGUiLCJhZGRFdmVudExpc3RlbmVyIiwic3RhdGUiLCJoYXNPd25Qcm9wZXJ0eSIsIm51bWJlciIsImRlY2ltYWxzIiwiZGVjX3BvaW50IiwidGhvdXNhbmRzX3NlcCIsIm4iLCJpc0Zpbml0ZSIsInByZWMiLCJNYXRoIiwiYWJzIiwic2VwIiwiZGVjIiwicyIsInRvRml4ZWRGaXgiLCJrIiwicG93Iiwicm91bmQiLCJBcnJheSIsImNsZWFuVXJsIiwicGFnZWQiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBLElBQU1BLFlBQVksR0FBR0EsWUFBWSxJQUFJO0FBQ3BDLFlBQVUsRUFEMEI7QUFFcEMsd0JBQXNCLEVBRmM7QUFHcEMsMEJBQXdCLEVBSFk7QUFJcEMsdUNBQXFDLEVBSkQ7QUFLcEMsOEJBQTRCLEVBTFE7QUFNcEMsZ0NBQThCLEVBTk07QUFPcEMsbUNBQWlDLEVBUEc7QUFRcEMsd0NBQXNDLEVBUkY7QUFTcEMsK0JBQTZCLEVBVE87QUFVcEMsMkNBQXlDLEVBVkw7QUFXcEMsc0NBQW9DLEVBWEE7QUFZcEMsdUNBQXFDLEVBWkQ7QUFhcEMsOENBQTRD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QUFDWjdNLElBQUFBLEdBQUcsR0FBR0EsR0FBRyxDQUFDQyxPQUFKLENBQWEsZUFBYixFQUE4QixFQUE5QixDQUFOO0FBQ0E7O0FBRUQsU0FBTzJNLFFBQVEsQ0FBRTVNLEdBQUYsQ0FBZjtBQUNBIiwiZmlsZSI6IndjLWFqYXgtcHJvZHVjdC1maWx0ZXItc2NyaXB0cy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogVGhlIG1haW4ganMgZmlsZS5cbiAqXG4gKiBAc2luY2UgICAgICAzLjAuMFxuICogQHBhY2thZ2UgICAgd2MtYWpheC1wcm9kdWN0LWZpbHRlclxuICogQHN1YnBhY2thZ2Ugd2MtYWpheC1wcm9kdWN0LWZpbHRlci9wdWJsaWMvc3JjL2pzXG4gKiBAYXV0aG9yICAgICB3cHRvb2xzLmlvXG4gKi9cblxuY29uc3Qgd2NhcGZfcGFyYW1zID0gd2NhcGZfcGFyYW1zIHx8IHtcblx0J2lzX3J0bCc6ICcnLFxuXHQnZmlsdGVyX2lucHV0X2RlbGF5JzogJycsXG5cdCdrZXl3b3JkX2ZpbHRlcl9kZWxheSc6ICcnLFxuXHQnY29tYm9ib3hfZGlzcGxheV9zZWxlY3RlZF9vcHRpb25zJzogJycsXG5cdCdjb21ib2JveF9ub19yZXN1bHRzX3RleHQnOiAnJyxcblx0J2NvbWJvYm94X29wdGlvbnNfbm9uZV90ZXh0JzogJycsXG5cdCdzZWFyY2hfYm94X2luX2RlZmF1bHRfb3JkZXJieSc6ICcnLFxuXHQncHJlc2VydmVfaGllcmFyY2h5X2FjY29yZGlvbl9zdGF0ZSc6ICcnLFxuXHQncHJlc2VydmVfc29mdF9saW1pdF9zdGF0ZSc6ICcnLFxuXHQnZW5hYmxlX2FuaW1hdGlvbl9mb3JfZmlsdGVyX2FjY29yZGlvbic6ICcnLFxuXHQnZmlsdGVyX2FjY29yZGlvbl9hbmltYXRpb25fc3BlZWQnOiAnJyxcblx0J2ZpbHRlcl9hY2NvcmRpb25fYW5pbWF0aW9uX2Vhc2luZyc6ICcnLFxuXHQnZW5hYmxlX2FuaW1hdGlvbl9mb3JfaGllcmFyY2h5X2FjY29yZGlvbic6ICcnLFxuXHQnaGllcmFyY2h5X2FjY29yZGlvbl9hbmltYXRpb25fc3BlZWQnOiAnJyxcblx0J2hpZXJhcmNoeV9hY2NvcmRpb25fYW5pbWF0aW9uX2Vhc2luZyc6ICcnLFxuXHQnc2Nyb2xsX3RvX3RvcF9zcGVlZCc6ICcnLFxuXHQnc2Nyb2xsX3RvX3RvcF9lYXNpbmcnOiAnJyxcblx0J2lzX21vYmlsZSc6ICcnLFxuXHQncmVsb2FkX29uX2JhY2snOiAnJyxcblx0J2ZvdW5kX3djYXBmJzogJycsXG5cdCd3Y2FwZl9wcm8nOiAnJyxcblx0J3VwZGF0ZV9kb2N1bWVudF90aXRsZSc6ICcnLFxuXHQndXNlX3RpcHB5anMnOiAnJyxcblx0J3Nob3BfbG9vcF9jb250YWluZXInOiAnJyxcblx0J25vdF9mb3VuZF9jb250YWluZXInOiAnJyxcblx0J3BhZ2luYXRpb25fY29udGFpbmVyJzogJycsXG5cdCdvcmRlcmJ5X2Zvcm0nOiAnJyxcblx0J29yZGVyYnlfZWxlbWVudCc6ICcnLFxuXHQnZGlzYWJsZV9hamF4JzogJycsXG5cdCdlbmFibGVfcGFnaW5hdGlvbl92aWFfYWpheCc6ICcnLFxuXHQnc29ydGluZ19jb250cm9sJzogJycsXG5cdCdhdHRhY2hfY29tYm9ib3hfb25fc29ydGluZyc6ICcnLFxuXHQnbG9hZGluZ19hbmltYXRpb24nOiAnJyxcblx0J3Njcm9sbF93aW5kb3cnOiAnJyxcblx0J3Njcm9sbF93aW5kb3dfZm9yJzogJycsXG5cdCdzY3JvbGxfd2luZG93X3doZW4nOiAnJyxcblx0J3Njcm9sbF93aW5kb3dfY3VzdG9tX2VsZW1lbnQnOiAnJyxcblx0J3Njcm9sbF9vbic6ICcnLFxuXHQnc2Nyb2xsX3RvX3RvcF9vZmZzZXQnOiAnJyxcblx0J2Rpc2FibGVfc2Nyb2xsX2FuaW1hdGlvbic6ICcnLFxuXHQnbW9yZV9zZWxlY3RvcnMnOiAnJyxcblx0J2N1c3RvbV9zY3JpcHRzJzogJycsXG59O1xuXG4oIGZ1bmN0aW9uKCAkLCB3aW5kb3cgKSB7XG5cblx0Y29uc3QgX2RlbGF5ID0gcGFyc2VJbnQoIHdjYXBmX3BhcmFtcy5maWx0ZXJfaW5wdXRfZGVsYXkgKTtcblx0Y29uc3QgZGVsYXkgID0gX2RlbGF5ID49IDAgPyBfZGVsYXkgOiAzMDA7XG5cblx0Y29uc3QgX2tleXdvcmRGaWx0ZXJEZWxheSA9IHBhcnNlSW50KCB3Y2FwZl9wYXJhbXMua2V5d29yZF9maWx0ZXJfZGVsYXkgKTtcblx0Y29uc3Qga2V5d29yZEZpbHRlckRlbGF5ICA9IF9rZXl3b3JkRmlsdGVyRGVsYXkgPj0gMCA/IF9rZXl3b3JkRmlsdGVyRGVsYXkgOiAxMDA7XG5cblx0Y29uc3QgaXNQcm8gPSB3Y2FwZl9wYXJhbXMud2NhcGZfcHJvO1xuXG5cdGNvbnN0ICRib2R5ICAgICA9ICQoICdib2R5JyApO1xuXHRjb25zdCAkZG9jdW1lbnQgPSAkKCBkb2N1bWVudCApO1xuXG5cdGNvbnN0IGluc3RhbmNlSWRzID0gW107XG5cblx0Y29uc3QgZGVmYXVsdE9yZGVyQnlFbGVtZW50ID0gd2NhcGZfcGFyYW1zLm9yZGVyYnlfZm9ybSArICcgJyArIHdjYXBmX3BhcmFtcy5vcmRlcmJ5X2VsZW1lbnQ7XG5cblx0JCggJy53Y2FwZi1maWx0ZXInICkuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0Y29uc3QgaWQgPSAkKCB0aGlzICkuZGF0YSggJ2lkJyApO1xuXG5cdFx0aWYgKCAhIGlkICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdGluc3RhbmNlSWRzLnB1c2goIGlkICk7XG5cdH0gKTtcblxuXHR3aW5kb3cudGlwcHlJbnN0YW5jZXMgPSBbXTtcblxuXHR3aW5kb3cuV0NBUEYgPSB3aW5kb3cuV0NBUEYgfHwge307XG5cblx0d2luZG93LldDQVBGID0ge1xuXHRcdGhhbmRsZUZpbHRlckFjY29yZGlvbjogZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zdCB0b2dnbGVBY2NvcmRpb24gPSAoICRlbCApID0+IHtcblx0XHRcdFx0Ly8gQ2hlY2sgdG8gc2VlIGlmIHRoZSBhY2NvcmRpb24gaXMgb3BlbmVkXG5cdFx0XHRcdGNvbnN0IHByZXNzZWQgPSAkZWwuYXR0ciggJ2FyaWEtZXhwYW5kZWQnICkgPT09ICd0cnVlJztcblxuXHRcdFx0XHQvLyBDaGFuZ2UgYXJpYS1leHBhbmRlZCB0byB0aGUgb3Bwb3NpdGUgc3RhdGVcblx0XHRcdFx0JGVsLmF0dHIoICdhcmlhLWV4cGFuZGVkJywgISBwcmVzc2VkICk7XG5cblx0XHRcdFx0Y29uc3QgJGZpbHRlcklubmVyID0gJGVsLmNsb3Nlc3QoICcud2NhcGYtZmlsdGVyJyApLmNoaWxkcmVuKCAnLndjYXBmLWZpbHRlci1pbm5lcicgKTtcblxuXHRcdFx0XHRpZiAoIHdjYXBmX3BhcmFtcy5lbmFibGVfYW5pbWF0aW9uX2Zvcl9maWx0ZXJfYWNjb3JkaW9uICkge1xuXHRcdFx0XHRcdCRmaWx0ZXJJbm5lci5zbGlkZVRvZ2dsZShcblx0XHRcdFx0XHRcdHdjYXBmX3BhcmFtcy5maWx0ZXJfYWNjb3JkaW9uX2FuaW1hdGlvbl9zcGVlZCxcblx0XHRcdFx0XHRcdHdjYXBmX3BhcmFtcy5maWx0ZXJfYWNjb3JkaW9uX2FuaW1hdGlvbl9lYXNpbmdcblx0XHRcdFx0XHQpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRmaWx0ZXJJbm5lci50b2dnbGUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblxuXHRcdFx0JGJvZHkub24oICdjbGljaycsICcud2NhcGYtZmlsdGVyLWFjY29yZGlvbi10cmlnZ2VyJywgZnVuY3Rpb24oIGUgKSB7XG5cdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdFx0dG9nZ2xlQWNjb3JkaW9uKCAkKCB0aGlzICkgKTtcblx0XHRcdH0gKTtcblxuXHRcdFx0JGJvZHkub24oICdjbGljaycsICcud2NhcGYtZmlsdGVyLXRpdGxlLmhhcy1hY2NvcmRpb24nLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3QgJHRyaWdnZXIgPSAkKCB0aGlzICkuZmluZCggJy53Y2FwZi1maWx0ZXItYWNjb3JkaW9uLXRyaWdnZXInICk7XG5cblx0XHRcdFx0dG9nZ2xlQWNjb3JkaW9uKCAkdHJpZ2dlciApO1xuXHRcdFx0fSApO1xuXHRcdH0sXG5cdFx0aGFuZGxlSGllcmFyY2h5VG9nZ2xlOiBmdW5jdGlvbigpIHtcblx0XHRcdGNvbnN0IHRvZ2dsZUFjY29yZGlvbiA9ICggJGVsICkgPT4ge1xuXHRcdFx0XHQvLyBDaGVjayB0byBzZWUgaWYgdGhlIGJ1dHRvbiBpcyBwcmVzc2VkXG5cdFx0XHRcdGNvbnN0IHByZXNzZWQgPSAkZWwuYXR0ciggJ2FyaWEtcHJlc3NlZCcgKSA9PT0gJ3RydWUnO1xuXG5cdFx0XHRcdC8vIENoYW5nZSBhcmlhLXByZXNzZWQgdG8gdGhlIG9wcG9zaXRlIHN0YXRlXG5cdFx0XHRcdCRlbC5hdHRyKCAnYXJpYS1wcmVzc2VkJywgISBwcmVzc2VkICk7XG5cblx0XHRcdFx0Y29uc3QgJGNoaWxkID0gJGVsLmNsb3Nlc3QoICdsaScgKS5jaGlsZHJlbiggJ3VsJyApO1xuXG5cdFx0XHRcdGlmICggd2NhcGZfcGFyYW1zLmVuYWJsZV9hbmltYXRpb25fZm9yX2hpZXJhcmNoeV9hY2NvcmRpb24gKSB7XG5cdFx0XHRcdFx0JGNoaWxkLnNsaWRlVG9nZ2xlKFxuXHRcdFx0XHRcdFx0d2NhcGZfcGFyYW1zLmhpZXJhcmNoeV9hY2NvcmRpb25fYW5pbWF0aW9uX3NwZWVkLFxuXHRcdFx0XHRcdFx0d2NhcGZfcGFyYW1zLmhpZXJhcmNoeV9hY2NvcmRpb25fYW5pbWF0aW9uX2Vhc2luZ1xuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGNoaWxkLnRvZ2dsZSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9O1xuXG5cdFx0XHQkYm9keVxuXHRcdFx0XHQub24oICdjbGljaycsICcud2NhcGYtaGllcmFyY2h5LWFjY29yZGlvbi10b2dnbGUnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR0b2dnbGVBY2NvcmRpb24oICQoIHRoaXMgKSApO1xuXHRcdFx0XHR9IClcblx0XHRcdFx0Lm9uKCAna2V5ZG93bicsICcud2NhcGYtaGllcmFyY2h5LWFjY29yZGlvbi10b2dnbGUnLCBmdW5jdGlvbiggZSApIHtcblx0XHRcdFx0XHRpZiAoIGUua2V5ID09PSAnICcgfHwgZS5rZXkgPT09ICdFbnRlcicgfHwgZS5rZXkgPT09ICdTcGFjZWJhcicgKSB7XG5cdFx0XHRcdFx0XHQvLyBQcmV2ZW50IHRoZSBkZWZhdWx0IGFjdGlvbiB0byBzdG9wIHNjcm9sbGluZyB3aGVuIHNwYWNlIGlzIHByZXNzZWRcblx0XHRcdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdFx0XHRcdFx0dG9nZ2xlQWNjb3JkaW9uKCAkKCB0aGlzICkgKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZVNvZnRMaW1pdDogZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zdCB0b2dnbGVGaWx0ZXJPcHRpb25zID0gKCAkZWwgKSA9PiB7XG5cdFx0XHRcdC8vIENoZWNrIHRvIHNlZSBpZiB0aGUgYnV0dG9uIGlzIHByZXNzZWRcblx0XHRcdFx0Y29uc3QgcHJlc3NlZCA9ICRlbC5hdHRyKCAnYXJpYS1wcmVzc2VkJyApID09PSAndHJ1ZSc7XG5cblx0XHRcdFx0Ly8gQ2hhbmdlIGFyaWEtcHJlc3NlZCB0byB0aGUgb3Bwb3NpdGUgc3RhdGVcblx0XHRcdFx0JGVsLmF0dHIoICdhcmlhLXByZXNzZWQnLCAhIHByZXNzZWQgKTtcblxuXHRcdFx0XHRjb25zdCAkbGlzdFdyYXBwZXIgPSAkZWwuY2xvc2VzdCggJy53Y2FwZi1saXN0LXdyYXBwZXInICk7XG5cblx0XHRcdFx0aWYgKCBwcmVzc2VkICkge1xuXHRcdFx0XHRcdCRsaXN0V3JhcHBlci5yZW1vdmVDbGFzcyggJ3Nob3ctaGlkZGVuLW9wdGlvbnMnICk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGxpc3RXcmFwcGVyLmFkZENsYXNzKCAnc2hvdy1oaWRkZW4tb3B0aW9ucycgKTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblxuXHRcdFx0JGJvZHlcblx0XHRcdFx0Lm9uKCAnY2xpY2snLCAnLndjYXBmLXNvZnQtbGltaXQtdHJpZ2dlcicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHRvZ2dsZUZpbHRlck9wdGlvbnMoICQoIHRoaXMgKSApO1xuXHRcdFx0XHR9IClcblx0XHRcdFx0Lm9uKCAna2V5ZG93bicsICcud2NhcGYtc29mdC1saW1pdC10cmlnZ2VyJywgZnVuY3Rpb24oIGUgKSB7XG5cdFx0XHRcdFx0aWYgKCBlLmtleSA9PT0gJyAnIHx8IGUua2V5ID09PSAnRW50ZXInIHx8IGUua2V5ID09PSAnU3BhY2ViYXInICkge1xuXHRcdFx0XHRcdFx0Ly8gUHJldmVudCB0aGUgZGVmYXVsdCBhY3Rpb24gdG8gc3RvcCBzY3JvbGxpbmcgd2hlbiBzcGFjZSBpcyBwcmVzc2VkXG5cdFx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdFx0XHRcdHRvZ2dsZUZpbHRlck9wdGlvbnMoICQoIHRoaXMgKSApO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSApO1xuXHRcdH0sXG5cdFx0aGFuZGxlU2VhcmNoRmlsdGVyT3B0aW9uczogZnVuY3Rpb24oKSB7XG5cdFx0XHQkYm9keS5vbiggJ2lucHV0JywgJy53Y2FwZi1zZWFyY2gtYm94IGlucHV0W3R5cGU9XCJ0ZXh0XCJdJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGNvbnN0ICR0aGF0ICAgPSAkKCB0aGlzICk7XG5cdFx0XHRcdGNvbnN0ICRpbm5lciAgPSAkdGhhdC5jbG9zZXN0KCAnLndjYXBmLWZpbHRlci1pbm5lcicgKTtcblx0XHRcdFx0Y29uc3QgJGZpbHRlciA9ICRpbm5lci5jbG9zZXN0KCAnLndjYXBmLWZpbHRlcicgKTtcblxuXHRcdFx0XHRjb25zdCBzb2Z0TGltaXRFbmFibGVkID0gJGZpbHRlci5oYXNDbGFzcyggJ2hhcy1zb2Z0LWxpbWl0JyApO1xuXHRcdFx0XHRjb25zdCBzb2Z0TGltaXRUb2dnbGUgID0gJGZpbHRlci5maW5kKCAnLndjYXBmLXNvZnQtbGltaXQtd3JhcHBlcicgKTtcblx0XHRcdFx0Y29uc3Qgbm9SZXN1bHRzICAgICAgICA9ICRmaWx0ZXIuZmluZCggJy53Y2FwZi1uby1yZXN1bHRzLXRleHQnICk7XG5cdFx0XHRcdGNvbnN0IHZpc2libGVPcHRpb25zICAgPSBwYXJzZUludCggJGZpbHRlci5hdHRyKCAnZGF0YS12aXNpYmxlLW9wdGlvbnMnICkgKTtcblxuXHRcdFx0XHRjb25zdCBrZXl3b3JkID0gJHRoYXQudmFsKCk7XG5cblx0XHRcdFx0aWYgKCAhIGtleXdvcmQubGVuZ3RoICkge1xuXHRcdFx0XHRcdGxldCBpbmRleCA9IDA7XG5cdFx0XHRcdFx0JGZpbHRlci5yZW1vdmVDbGFzcyggJ3NlYXJjaC1hY3RpdmUnICk7XG5cblx0XHRcdFx0XHQkLmVhY2goICRpbm5lci5maW5kKCAnLndjYXBmLWZpbHRlci1vcHRpb25zID4gbGknICksIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0aW5kZXgrKztcblxuXHRcdFx0XHRcdFx0Y29uc3QgJGZpbHRlckl0ZW0gPSAkKCB0aGlzICk7XG5cdFx0XHRcdFx0XHQkZmlsdGVySXRlbS5yZW1vdmVDbGFzcyggJ2tleXdvcmQtbWF0Y2hlZCcgKTtcblxuXHRcdFx0XHRcdFx0aWYgKCBzb2Z0TGltaXRFbmFibGVkICkge1xuXHRcdFx0XHRcdFx0XHRpZiAoIGluZGV4ID4gdmlzaWJsZU9wdGlvbnMgKSB7XG5cdFx0XHRcdFx0XHRcdFx0JGZpbHRlckl0ZW0uYWRkQ2xhc3MoICd3Y2FwZi1maWx0ZXItb3B0aW9uLWhpZGRlbicgKTtcblx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0XHQkZmlsdGVySXRlbS5yZW1vdmVDbGFzcyggJ3djYXBmLWZpbHRlci1vcHRpb24taGlkZGVuJyApO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSApO1xuXG5cdFx0XHRcdFx0aWYgKCBzb2Z0TGltaXRFbmFibGVkICkge1xuXHRcdFx0XHRcdFx0c29mdExpbWl0VG9nZ2xlLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRub1Jlc3VsdHMuY2hpbGRyZW4oICdzcGFuJyApLnRleHQoICcnICk7XG5cdFx0XHRcdFx0bm9SZXN1bHRzLmhpZGUoKTtcblxuXHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGxldCBpbmRleCA9IDA7XG5cdFx0XHRcdCRmaWx0ZXIuYWRkQ2xhc3MoICdzZWFyY2gtYWN0aXZlJyApO1xuXG5cdFx0XHRcdCQuZWFjaCggJGlubmVyLmZpbmQoICcud2NhcGYtZmlsdGVyLW9wdGlvbnMgPiBsaScgKSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0Y29uc3QgJGZpbHRlckl0ZW0gPSAkKCB0aGlzICk7XG5cdFx0XHRcdFx0Y29uc3QgbGFiZWwgICAgICAgPSAkZmlsdGVySXRlbS5maW5kKCAnLndjYXBmLWZpbHRlci1pdGVtLWxhYmVsJyApLmRhdGEoICdsYWJlbCcgKTtcblxuXHRcdFx0XHRcdGlmICggbGFiZWwudG9TdHJpbmcoKS50b0xvd2VyQ2FzZSgpLmluY2x1ZGVzKCBrZXl3b3JkLnRvTG93ZXJDYXNlKCkgKSApIHtcblx0XHRcdFx0XHRcdGluZGV4Kys7XG5cblx0XHRcdFx0XHRcdCRmaWx0ZXJJdGVtLmFkZENsYXNzKCAna2V5d29yZC1tYXRjaGVkJyApO1xuXG5cdFx0XHRcdFx0XHRpZiAoIHNvZnRMaW1pdEVuYWJsZWQgKSB7XG5cdFx0XHRcdFx0XHRcdGlmICggaW5kZXggPiB2aXNpYmxlT3B0aW9ucyApIHtcblx0XHRcdFx0XHRcdFx0XHQkZmlsdGVySXRlbS5hZGRDbGFzcyggJ3djYXBmLWZpbHRlci1vcHRpb24taGlkZGVuJyApO1xuXHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdCRmaWx0ZXJJdGVtLnJlbW92ZUNsYXNzKCAnd2NhcGYtZmlsdGVyLW9wdGlvbi1oaWRkZW4nICk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0JGZpbHRlckl0ZW0ucmVtb3ZlQ2xhc3MoICdrZXl3b3JkLW1hdGNoZWQnICk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0aWYgKCBzb2Z0TGltaXRFbmFibGVkICkge1xuXHRcdFx0XHRcdGlmICggaW5kZXggPD0gdmlzaWJsZU9wdGlvbnMgKSB7XG5cdFx0XHRcdFx0XHRzb2Z0TGltaXRUb2dnbGUuaGlkZSgpO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRzb2Z0TGltaXRUb2dnbGUuc2hvdygpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXG5cdFx0XHRcdGlmICggMCA9PT0gaW5kZXggKSB7XG5cdFx0XHRcdFx0bm9SZXN1bHRzLmNoaWxkcmVuKCAnc3BhbicgKS50ZXh0KCBrZXl3b3JkICk7XG5cdFx0XHRcdFx0bm9SZXN1bHRzLnNob3coKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRub1Jlc3VsdHMuY2hpbGRyZW4oICdzcGFuJyApLnRleHQoICcnICk7XG5cdFx0XHRcdFx0bm9SZXN1bHRzLmhpZGUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSApO1xuXG5cdFx0XHQkYm9keS5vbiggJ2NsaWNrJywgJy53Y2FwZi1zZWFyY2gtYm94IC53Y2FwZi1jbGVhci1zdGF0ZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRjb25zdCAkdGhhdCAgICAgID0gJCggdGhpcyApO1xuXHRcdFx0XHRjb25zdCAkc2VhcmNoQm94ID0gJHRoYXQuY2xvc2VzdCggJy53Y2FwZi1zZWFyY2gtYm94JyApO1xuXHRcdFx0XHRjb25zdCAkaW5wdXQgICAgID0gJHNlYXJjaEJveC5maW5kKCAnaW5wdXRbdHlwZT1cInRleHRcIl0nICk7XG5cdFx0XHRcdGNvbnN0ICRmaWx0ZXIgICAgPSAkc2VhcmNoQm94LmNsb3Nlc3QoICcud2NhcGYtZmlsdGVyJyApO1xuXG5cdFx0XHRcdCRpbnB1dC52YWwoICcnICk7XG5cblx0XHRcdFx0aWYgKCAkZmlsdGVyLmhhc0NsYXNzKCAnd2NhcGYtZmlsdGVyLWtleXdvcmQnICkgKSB7XG5cdFx0XHRcdFx0JGlucHV0LnRyaWdnZXIoICdjaGFuZ2UnICk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGlucHV0LnRyaWdnZXIoICdpbnB1dCcgKTtcblx0XHRcdFx0fVxuXHRcdFx0fSApO1xuXG5cdFx0XHQkYm9keS5vbiggJ2NoYW5nZScsICcud2NhcGYtZmlsdGVyLWtleXdvcmQgaW5wdXRbdHlwZT1cInRleHRcIl0nLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3QgJHRoYXQgICAgPSAkKCB0aGlzICk7XG5cdFx0XHRcdGNvbnN0ICR3cmFwcGVyID0gJHRoYXQuY2xvc2VzdCggJy53Y2FwZi1rZXl3b3JkLWZpbHRlci13cmFwcGVyJyApO1xuXHRcdFx0XHRjb25zdCBrZXl3b3JkICA9ICR0aGF0LnZhbCgpO1xuXG5cdFx0XHRcdC8vIENsZWFyIGFueSBwcmV2aW91c2x5IHNldCB0aW1lciBiZWZvcmUgc2V0dGluZyBhIGZyZXNoIG9uZVxuXHRcdFx0XHRjbGVhclRpbWVvdXQoICR0aGF0LmRhdGEoICd0aW1lcicgKSApO1xuXG5cdFx0XHRcdGNvbnN0IGZpbHRlclVSTCAgICAgID0gJHdyYXBwZXIuZGF0YSggJ2ZpbHRlci11cmwnICk7XG5cdFx0XHRcdGNvbnN0IGNsZWFyRmlsdGVyVVJMID0gJHdyYXBwZXIuZGF0YSggJ2NsZWFyLWZpbHRlci11cmwnICk7XG5cdFx0XHRcdGxldCB1cmw7XG5cblx0XHRcdFx0aWYgKCBrZXl3b3JkLmxlbmd0aCApIHtcblx0XHRcdFx0XHR1cmwgPSBmaWx0ZXJVUkwucmVwbGFjZSggJyVzJywga2V5d29yZCApO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdHVybCA9IGNsZWFyRmlsdGVyVVJMO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0JHRoYXQuZGF0YSggJ3RpbWVyJywgc2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0V0NBUEYucmVxdWVzdEZpbHRlciggdXJsICk7XG5cdFx0XHRcdH0sIGtleXdvcmRGaWx0ZXJEZWxheSApICk7XG5cdFx0XHR9ICk7XG5cdFx0fSxcblx0XHR1cGRhdGVQcm9kdWN0c0NvdW50UmVzdWx0OiBmdW5jdGlvbiggJHJlc3BvbnNlICkge1xuXHRcdFx0Y29uc3QgJGNvbnRhaW5lciA9ICQoIHdjYXBmX3BhcmFtcy5zaG9wX2xvb3BfY29udGFpbmVyICk7XG5cdFx0XHRjb25zdCBzZWxlY3RvciAgID0gJy53b29jb21tZXJjZS1yZXN1bHQtY291bnQnO1xuXHRcdFx0Y29uc3QgbmV3Q291bnQgICA9ICRyZXNwb25zZS5maW5kKCBzZWxlY3RvciApLmh0bWwoKTtcblxuXHRcdFx0JGJvZHkuZmluZCggc2VsZWN0b3IgKS5lYWNoKCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3QgJGVsID0gJCggdGhpcyApO1xuXG5cdFx0XHRcdGlmICggISAkY29udGFpbmVyLmhhcyggJGVsICkubGVuZ3RoICkge1xuXHRcdFx0XHRcdCRlbC5odG1sKCBuZXdDb3VudCApO1xuXHRcdFx0XHR9XG5cdFx0XHR9ICk7XG5cdFx0fSxcblx0XHRzY3JvbGxUbzogZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoICdub25lJyA9PT0gd2NhcGZfcGFyYW1zLnNjcm9sbF93aW5kb3cgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0Y29uc3Qgc2Nyb2xsRm9yID0gd2NhcGZfcGFyYW1zLnNjcm9sbF93aW5kb3dfZm9yO1xuXHRcdFx0Y29uc3QgaXNNb2JpbGUgID0gd2NhcGZfcGFyYW1zLmlzX21vYmlsZTtcblx0XHRcdGxldCBwcm9jZWVkICAgICA9IGZhbHNlO1xuXG5cdFx0XHRpZiAoICdtb2JpbGUnID09PSBzY3JvbGxGb3IgJiYgaXNNb2JpbGUgKSB7XG5cdFx0XHRcdHByb2NlZWQgPSB0cnVlO1xuXHRcdFx0fSBlbHNlIGlmICggJ2Rlc2t0b3AnID09PSBzY3JvbGxGb3IgJiYgISBpc01vYmlsZSApIHtcblx0XHRcdFx0cHJvY2VlZCA9IHRydWU7XG5cdFx0XHR9IGVsc2UgaWYgKCAnYm90aCcgPT09IHNjcm9sbEZvciApIHtcblx0XHRcdFx0cHJvY2VlZCA9IHRydWU7XG5cdFx0XHR9XG5cblx0XHRcdGlmICggISBwcm9jZWVkICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGxldCBhZGp1c3RpbmdPZmZzZXQgPSAwLCBvZmZzZXQgPSAwO1xuXG5cdFx0XHRpZiAoIHdjYXBmX3BhcmFtcy5zY3JvbGxfdG9fdG9wX29mZnNldCApIHtcblx0XHRcdFx0YWRqdXN0aW5nT2Zmc2V0ID0gcGFyc2VJbnQoIHdjYXBmX3BhcmFtcy5zY3JvbGxfdG9fdG9wX29mZnNldCApO1xuXHRcdFx0fVxuXG5cdFx0XHRsZXQgY29udGFpbmVyO1xuXG5cdFx0XHRpZiAoICQoIHdjYXBmX3BhcmFtcy5zaG9wX2xvb3BfY29udGFpbmVyICkubGVuZ3RoICkge1xuXHRcdFx0XHRjb250YWluZXIgPSB3Y2FwZl9wYXJhbXMuc2hvcF9sb29wX2NvbnRhaW5lcjtcblx0XHRcdH0gZWxzZSBpZiAoICQoIHdjYXBmX3BhcmFtcy5ub3RfZm91bmRfY29udGFpbmVyICkubGVuZ3RoICkge1xuXHRcdFx0XHRjb250YWluZXIgPSB3Y2FwZl9wYXJhbXMubm90X2ZvdW5kX2NvbnRhaW5lcjtcblx0XHRcdH1cblxuXHRcdFx0aWYgKCAnY3VzdG9tJyA9PT0gd2NhcGZfcGFyYW1zLnNjcm9sbF93aW5kb3cgKSB7XG5cdFx0XHRcdGNvbnRhaW5lciA9IHdjYXBmX3BhcmFtcy5zY3JvbGxfd2luZG93X2N1c3RvbV9lbGVtZW50O1xuXHRcdFx0fVxuXG5cdFx0XHRjb25zdCAkY29udGFpbmVyID0gJCggY29udGFpbmVyICk7XG5cblx0XHRcdGlmICggJGNvbnRhaW5lci5sZW5ndGggKSB7XG5cdFx0XHRcdG9mZnNldCA9ICRjb250YWluZXIub2Zmc2V0KCkudG9wIC0gYWRqdXN0aW5nT2Zmc2V0O1xuXG5cdFx0XHRcdGlmICggb2Zmc2V0IDwgMCApIHtcblx0XHRcdFx0XHRvZmZzZXQgPSAwO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0JCggJ2h0bWwsIGJvZHknICkuc3RvcCgpLmFuaW1hdGUoXG5cdFx0XHRcdFx0eyBzY3JvbGxUb3A6IG9mZnNldCB9LFxuXHRcdFx0XHRcdHdjYXBmX3BhcmFtcy5zY3JvbGxfdG9fdG9wX3NwZWVkLFxuXHRcdFx0XHRcdHdjYXBmX3BhcmFtcy5zY3JvbGxfdG9fdG9wX2Vhc2luZ1xuXHRcdFx0XHQpO1xuXHRcdFx0fVxuXHRcdH0sXG5cdFx0Ly8gVGhpbmdzIGFyZSBkb25lIGJlZm9yZSBmZXRjaGluZyB0aGUgcHJvZHVjdHMgbGlrZSBzaG93aW5nIHRoZSBsb2FkaW5nIGluZGljYXRvci5cblx0XHRiZWZvcmVGZXRjaGluZ1Byb2R1Y3RzOiBmdW5jdGlvbiggdHJpZ2dlcmVkQnkgKSB7XG5cdFx0XHQkYm9keS5maW5kKCAnLndjYXBmLWxvYWRlcicgKS5hZGRDbGFzcyggJ2lzLWFjdGl2ZScgKTtcblxuXHRcdFx0aWYgKCAhIGlzUHJvICYmICdpbW1lZGlhdGVseScgPT09IHdjYXBmX3BhcmFtcy5zY3JvbGxfd2luZG93X3doZW4gKSB7XG5cdFx0XHRcdFdDQVBGLnNjcm9sbFRvKCk7XG5cdFx0XHR9XG5cblx0XHRcdCRkb2N1bWVudC50cmlnZ2VyKCAnd2NhcGZfYmVmb3JlX2ZldGNoaW5nX3Byb2R1Y3RzJywgWyB0cmlnZ2VyZWRCeSBdICk7XG5cdFx0fSxcblx0XHRkZXN0cm95VGlwcHlJbnN0YW5jZXM6IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMudXNlX3RpcHB5anMgKSB7XG5cdFx0XHRcdC8vIEBzb3VyY2UgaHR0cHM6Ly9naXRodWIuY29tL2F0b21pa3MvdGlwcHlqcy9pc3N1ZXMvNDczXG5cdFx0XHRcdHRpcHB5SW5zdGFuY2VzLmZvckVhY2goIGluc3RhbmNlID0+IHtcblx0XHRcdFx0XHRpbnN0YW5jZS5kZXN0cm95KCk7XG5cdFx0XHRcdH0gKTtcblx0XHRcdFx0dGlwcHlJbnN0YW5jZXMubGVuZ3RoID0gMDsgLy8gY2xlYXIgaXRcblx0XHRcdH1cblx0XHR9LFxuXHRcdC8vIFRoaW5ncyBhcmUgZG9uZSBiZWZvcmUgdXBkYXRpbmcgdGhlIHByb2R1Y3RzIGxpa2UgaGlkaW5nIHRoZSBsb2FkaW5nIGluZGljYXRvci5cblx0XHRiZWZvcmVVcGRhdGluZ1Byb2R1Y3RzOiBmdW5jdGlvbiggJHJlc3BvbnNlLCB0cmlnZ2VyZWRCeSApIHtcblx0XHRcdCRib2R5LmZpbmQoICcud2NhcGYtbG9hZGVyJyApLnJlbW92ZUNsYXNzKCAnaXMtYWN0aXZlJyApO1xuXG5cdFx0XHQvLyBNYXliZSBnb29kIGZvciBwZXJmb3JtYW5jZS5cblx0XHRcdFdDQVBGLmRlc3Ryb3lUaXBweUluc3RhbmNlcygpO1xuXG5cdFx0XHQkZG9jdW1lbnQudHJpZ2dlciggJ3djYXBmX2JlZm9yZV91cGRhdGluZ19wcm9kdWN0cycsIFsgJHJlc3BvbnNlLCB0cmlnZ2VyZWRCeSBdICk7XG5cdFx0fSxcblx0XHRhZnRlclVwZGF0aW5nUHJvZHVjdHM6IGZ1bmN0aW9uKCAkcmVzcG9uc2UsIHRyaWdnZXJlZEJ5ICkge1xuXHRcdFx0V0NBUEYudXBkYXRlUHJvZHVjdHNDb3VudFJlc3VsdCggJHJlc3BvbnNlICk7XG5cblx0XHRcdC8vIFJlaW5pdGlhbGl6ZSB3Y2FwZi5cblx0XHRcdFdDQVBGLmluaXQoKTtcblxuXHRcdFx0aWYgKCAhIGlzUHJvICYmICdhZnRlcicgPT09IHdjYXBmX3BhcmFtcy5zY3JvbGxfd2luZG93X3doZW4gKSB7XG5cdFx0XHRcdFdDQVBGLnNjcm9sbFRvKCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIFRyaWdnZXIgZXZlbnRzLlxuXHRcdFx0JCggZG9jdW1lbnQgKS50cmlnZ2VyKCAncmVhZHknICk7XG5cdFx0XHQkKCB3aW5kb3cgKS50cmlnZ2VyKCAnc2Nyb2xsJyApO1xuXHRcdFx0JCggd2luZG93ICkudHJpZ2dlciggJ3Jlc2l6ZScgKTtcblxuXHRcdFx0Ly8gQTMgTGF6eSBMb2FkIHN1cHBvcnQuXG5cdFx0XHQkKCB3aW5kb3cgKS50cmlnZ2VyKCAnbGF6eXNob3cnICk7XG5cblx0XHRcdGlmICggd2NhcGZfcGFyYW1zLmN1c3RvbV9zY3JpcHRzICkge1xuXHRcdFx0XHRldmFsKCB3Y2FwZl9wYXJhbXMuY3VzdG9tX3NjcmlwdHMgKTtcblx0XHRcdH1cblxuXHRcdFx0JGRvY3VtZW50LnRyaWdnZXIoICd3Y2FwZl9hZnRlcl91cGRhdGluZ19wcm9kdWN0cycsIFsgJHJlc3BvbnNlLCB0cmlnZ2VyZWRCeSBdICk7XG5cdFx0fSxcblx0XHRmaWx0ZXJQcm9kdWN0czogZnVuY3Rpb24oIHRyaWdnZXJlZEJ5ID0gJ2ZpbHRlcicgKSB7XG5cdFx0XHRXQ0FQRi5iZWZvcmVGZXRjaGluZ1Byb2R1Y3RzKCB0cmlnZ2VyZWRCeSApO1xuXG5cdFx0XHQkLmFqYXgoIHtcblx0XHRcdFx0dXJsOiB3aW5kb3cubG9jYXRpb24uaHJlZixcblx0XHRcdFx0c3VjY2VzczogZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuXHRcdFx0XHRcdGNvbnN0ICRyZXNwb25zZSA9ICQoIHJlc3BvbnNlICk7XG5cblx0XHRcdFx0XHRXQ0FQRi5iZWZvcmVVcGRhdGluZ1Byb2R1Y3RzKCAkcmVzcG9uc2UsIHRyaWdnZXJlZEJ5ICk7XG5cblx0XHRcdFx0XHQvKipcblx0XHRcdFx0XHQgKiBVcGRhdGUgZG9jdW1lbnQgdGl0bGUuXG5cdFx0XHRcdFx0ICpcblx0XHRcdFx0XHQgKiBAc291cmNlIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vYS83NTk5NTYyXG5cdFx0XHRcdFx0ICovXG5cdFx0XHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMudXBkYXRlX2RvY3VtZW50X3RpdGxlICkge1xuXHRcdFx0XHRcdFx0ZG9jdW1lbnQudGl0bGUgPSAkcmVzcG9uc2UuZmlsdGVyKCAndGl0bGUnICkudGV4dCgpO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIFVwZGF0ZSB0aGUgaW5zdGFuY2VzLlxuXHRcdFx0XHRcdGZvciAoIGNvbnN0IGlkIG9mIGluc3RhbmNlSWRzICkge1xuXHRcdFx0XHRcdFx0Y29uc3QgaW5zdGFuY2VJZCA9ICdbZGF0YS1pZD1cIicgKyBpZCArICdcIl0nO1xuXHRcdFx0XHRcdFx0Y29uc3QgJGluc3RhbmNlICA9ICQoIGluc3RhbmNlSWQgKTtcblx0XHRcdFx0XHRcdGNvbnN0ICRpbm5lciAgICAgPSAkaW5zdGFuY2UuZmluZCggJy53Y2FwZi1maWx0ZXItaW5uZXInICk7XG5cdFx0XHRcdFx0XHRjb25zdCBfaW5zdGFuY2UgID0gJHJlc3BvbnNlLmZpbmQoIGluc3RhbmNlSWQgKTtcblxuXHRcdFx0XHRcdFx0Ly8gUHJlc2VydmUgaGllcmFyY2h5IGFjY29yZGlvbiBzdGF0ZS5cblx0XHRcdFx0XHRcdGlmICggd2NhcGZfcGFyYW1zLnByZXNlcnZlX2hpZXJhcmNoeV9hY2NvcmRpb25fc3RhdGUgKSB7XG5cdFx0XHRcdFx0XHRcdGlmICggJGluc3RhbmNlLmhhc0NsYXNzKCAnaGFzLWhpZXJhcmNoeS1hY2NvcmRpb24nICkgKSB7XG5cdFx0XHRcdFx0XHRcdFx0JGluc3RhbmNlLmZpbmQoICcud2NhcGYtaGllcmFyY2h5LWFjY29yZGlvbi10b2dnbGUnICkuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRjb25zdCAkZWwgPSAkKCB0aGlzICk7XG5cdFx0XHRcdFx0XHRcdFx0XHRjb25zdCBpZCAgPSAkZWwuZGF0YSggJ2lkJyApO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHRjb25zdCB0b2dnbGVTZWxlY3RvciA9IGAud2NhcGYtaGllcmFyY2h5LWFjY29yZGlvbi10b2dnbGVbZGF0YS1pZD1cIiR7IGlkIH1cIl1gO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBDaGVjayB0byBzZWUgaWYgdGhlIGFjY29yZGlvbiBpcyBvcGVuZWRcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnN0IHByZXNzZWQgPSAkZWwuYXR0ciggJ2FyaWEtcHJlc3NlZCcgKSA9PT0gJ3RydWUnO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHRpZiAoIHByZXNzZWQgKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdF9pbnN0YW5jZS5maW5kKCB0b2dnbGVTZWxlY3RvciApLmF0dHIoICdhcmlhLXByZXNzZWQnLCAndHJ1ZScgKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0X2luc3RhbmNlLmZpbmQoIHRvZ2dsZVNlbGVjdG9yICkuY2xvc2VzdCggJ2xpJyApLmNoaWxkcmVuKCAndWwnICkuc2hvdygpO1xuXHRcdFx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0X2luc3RhbmNlLmZpbmQoIHRvZ2dsZVNlbGVjdG9yICkuYXR0ciggJ2FyaWEtcHJlc3NlZCcsICdmYWxzZScgKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0X2luc3RhbmNlLmZpbmQoIHRvZ2dsZVNlbGVjdG9yICkuY2xvc2VzdCggJ2xpJyApLmNoaWxkcmVuKCAndWwnICkuaGlkZSgpO1xuXHRcdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdH0gKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHQvLyBQcmVzZXJ2ZSBzb2Z0IGxpbWl0IHN0YXRlLlxuXHRcdFx0XHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMucHJlc2VydmVfc29mdF9saW1pdF9zdGF0ZSApIHtcblx0XHRcdFx0XHRcdFx0aWYgKCAkaW5zdGFuY2UuaGFzQ2xhc3MoICdoYXMtc29mdC1saW1pdCcgKSApIHtcblx0XHRcdFx0XHRcdFx0XHRjb25zdCAkbGlzdFdyYXBwZXIgPSAkaW5zdGFuY2UuZmluZCggJy53Y2FwZi1saXN0LXdyYXBwZXInICk7XG5cblx0XHRcdFx0XHRcdFx0XHRpZiAoICRsaXN0V3JhcHBlci5oYXNDbGFzcyggJ3Nob3ctaGlkZGVuLW9wdGlvbnMnICkgKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRfaW5zdGFuY2UuZmluZCggJy53Y2FwZi1saXN0LXdyYXBwZXInICkuYWRkQ2xhc3MoICdzaG93LWhpZGRlbi1vcHRpb25zJyApO1xuXHRcdFx0XHRcdFx0XHRcdFx0X2luc3RhbmNlLmZpbmQoICcud2NhcGYtc29mdC1saW1pdC10cmlnZ2VyJyApLmF0dHIoICdhcmlhLXByZXNzZWQnLCAndHJ1ZScgKTtcblx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdFx0X2luc3RhbmNlLmZpbmQoICcud2NhcGYtbGlzdC13cmFwcGVyJyApLnJlbW92ZUNsYXNzKCAnc2hvdy1oaWRkZW4tb3B0aW9ucycgKTtcblx0XHRcdFx0XHRcdFx0XHRcdF9pbnN0YW5jZS5maW5kKCAnLndjYXBmLXNvZnQtbGltaXQtdHJpZ2dlcicgKS5hdHRyKCAnYXJpYS1wcmVzc2VkJywgJ2ZhbHNlJyApO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHRjb25zdCBfaHRtbCA9IF9pbnN0YW5jZS5maW5kKCAnLndjYXBmLWZpbHRlci1pbm5lcicgKS5odG1sKCk7XG5cblx0XHRcdFx0XHRcdC8vIEZpbmFsbHkgdXBkYXRlIHRoZSBpbnN0YW5jZS5cblx0XHRcdFx0XHRcdCRpbm5lci5odG1sKCBfaHRtbCApO1xuXG5cdFx0XHRcdFx0XHQkaW5zdGFuY2UudHJpZ2dlciggJ3djYXBmLWZpbHRlci11cGRhdGVkJywgWyBfaW5zdGFuY2UgXSApO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIFVwZGF0ZSB0aGUgYWN0aXZlIGZpbHRlcnMgYW5kIHJlc2V0IGZpbHRlcnMuXG5cdFx0XHRcdFx0JGJvZHkuZmluZCggJy53Y2FwZi1hY3RpdmUtZmlsdGVycywgLndjYXBmLXJlc2V0LWZpbHRlcnMnICkuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRjb25zdCAkdGhhdCAgICAgID0gJCggdGhpcyApO1xuXHRcdFx0XHRcdFx0Y29uc3QgaW5zdGFuY2VJZCA9ICdbZGF0YS1pZD1cIicgKyAkdGhhdC5kYXRhKCAnaWQnICkgKyAnXCJdJztcblxuXHRcdFx0XHRcdFx0JHRoYXQuaHRtbCggJHJlc3BvbnNlLmZpbmQoIGluc3RhbmNlSWQgKS5odG1sKCkgKTtcblx0XHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0XHQvLyBSZXBsYWNlIG9sZCBzaG9wIGxvb3Agd2l0aCBuZXcgb25lLlxuXHRcdFx0XHRcdGNvbnN0ICRzaG9wTG9vcENvbnRhaW5lciA9ICRyZXNwb25zZS5maW5kKCB3Y2FwZl9wYXJhbXMuc2hvcF9sb29wX2NvbnRhaW5lciApO1xuXHRcdFx0XHRcdGNvbnN0ICRub3RGb3VuZENvbnRhaW5lciA9ICRyZXNwb25zZS5maW5kKCB3Y2FwZl9wYXJhbXMubm90X2ZvdW5kX2NvbnRhaW5lciApO1xuXG5cdFx0XHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMuc2hvcF9sb29wX2NvbnRhaW5lciA9PT0gd2NhcGZfcGFyYW1zLm5vdF9mb3VuZF9jb250YWluZXIgKSB7XG5cdFx0XHRcdFx0XHQkKCB3Y2FwZl9wYXJhbXMuc2hvcF9sb29wX2NvbnRhaW5lciApLmh0bWwoICRzaG9wTG9vcENvbnRhaW5lci5odG1sKCkgKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0aWYgKCAkKCB3Y2FwZl9wYXJhbXMubm90X2ZvdW5kX2NvbnRhaW5lciApLmxlbmd0aCApIHtcblx0XHRcdFx0XHRcdFx0aWYgKCAkc2hvcExvb3BDb250YWluZXIubGVuZ3RoICkge1xuXHRcdFx0XHRcdFx0XHRcdCQoIHdjYXBmX3BhcmFtcy5ub3RfZm91bmRfY29udGFpbmVyICkuaHRtbCggJHNob3BMb29wQ29udGFpbmVyLmh0bWwoKSApO1xuXHRcdFx0XHRcdFx0XHR9IGVsc2UgaWYgKCAkbm90Rm91bmRDb250YWluZXIubGVuZ3RoICkge1xuXHRcdFx0XHRcdFx0XHRcdCQoIHdjYXBmX3BhcmFtcy5ub3RfZm91bmRfY29udGFpbmVyICkuaHRtbCggJG5vdEZvdW5kQ29udGFpbmVyLmh0bWwoKSApO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9IGVsc2UgaWYgKCAkKCB3Y2FwZl9wYXJhbXMuc2hvcF9sb29wX2NvbnRhaW5lciApLmxlbmd0aCApIHtcblx0XHRcdFx0XHRcdFx0aWYgKCAkc2hvcExvb3BDb250YWluZXIubGVuZ3RoICkge1xuXHRcdFx0XHRcdFx0XHRcdCQoIHdjYXBmX3BhcmFtcy5zaG9wX2xvb3BfY29udGFpbmVyICkuaHRtbCggJHNob3BMb29wQ29udGFpbmVyLmh0bWwoKSApO1xuXHRcdFx0XHRcdFx0XHR9IGVsc2UgaWYgKCAkbm90Rm91bmRDb250YWluZXIubGVuZ3RoICkge1xuXHRcdFx0XHRcdFx0XHRcdCQoIHdjYXBmX3BhcmFtcy5zaG9wX2xvb3BfY29udGFpbmVyICkuaHRtbCggJG5vdEZvdW5kQ29udGFpbmVyLmh0bWwoKSApO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0V0NBUEYuYWZ0ZXJVcGRhdGluZ1Byb2R1Y3RzKCAkcmVzcG9uc2UsIHRyaWdnZXJlZEJ5ICk7XG5cdFx0XHRcdH1cblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdHJlcXVlc3RGaWx0ZXI6IGZ1bmN0aW9uKCB1cmwsIHRyaWdnZXJlZEJ5ID0gJ2ZpbHRlcicgKSB7XG5cdFx0XHRpZiAoICEgdXJsICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGlmICggd2NhcGZfcGFyYW1zLmRpc2FibGVfYWpheCApIHtcblx0XHRcdFx0d2luZG93LmxvY2F0aW9uLmhyZWYgPSB1cmw7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRoaXN0b3J5LnB1c2hTdGF0ZSggeyB3Y2FwZjogdHJ1ZSB9LCAnJywgdXJsICk7XG5cblx0XHRcdFx0V0NBUEYuZmlsdGVyUHJvZHVjdHMoIHRyaWdnZXJlZEJ5ICk7XG5cdFx0XHR9XG5cdFx0fSxcblx0XHRoYW5kbGVOdW1iZXJJbnB1dEZpbHRlcnM6IGZ1bmN0aW9uKCkge1xuXHRcdFx0Y29uc3QgcmFuZ2VOdW1iZXJTZWxlY3RvcnMgPSAnLndjYXBmLXJhbmdlLW51bWJlciAubWluLXZhbHVlLCAud2NhcGYtcmFuZ2UtbnVtYmVyIC5tYXgtdmFsdWUnO1xuXG5cdFx0XHQkYm9keS5vbiggJ2lucHV0JywgcmFuZ2VOdW1iZXJTZWxlY3RvcnMsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRjb25zdCAkaXRlbSA9ICQoIHRoaXMgKTtcblxuXHRcdFx0XHRjb25zdCAkcmFuZ2VOdW1iZXIgICAgICA9ICRpdGVtLmNsb3Nlc3QoICcud2NhcGYtcmFuZ2UtbnVtYmVyJyApO1xuXHRcdFx0XHRjb25zdCBmb3JtYXROdW1iZXJzICAgICA9ICRyYW5nZU51bWJlci5hdHRyKCAnZGF0YS1mb3JtYXQtbnVtYmVycycgKTtcblx0XHRcdFx0Y29uc3QgcmFuZ2VNaW5WYWx1ZSAgICAgPSBwYXJzZUZsb2F0KCAkcmFuZ2VOdW1iZXIuYXR0ciggJ2RhdGEtcmFuZ2UtbWluLXZhbHVlJyApICk7XG5cdFx0XHRcdGNvbnN0IHJhbmdlTWF4VmFsdWUgICAgID0gcGFyc2VGbG9hdCggJHJhbmdlTnVtYmVyLmF0dHIoICdkYXRhLXJhbmdlLW1heC12YWx1ZScgKSApO1xuXHRcdFx0XHRjb25zdCBvbGRNaW5WYWx1ZSAgICAgICA9IHBhcnNlRmxvYXQoICRyYW5nZU51bWJlci5hdHRyKCAnZGF0YS1taW4tdmFsdWUnICkgKTtcblx0XHRcdFx0Y29uc3Qgb2xkTWF4VmFsdWUgICAgICAgPSBwYXJzZUZsb2F0KCAkcmFuZ2VOdW1iZXIuYXR0ciggJ2RhdGEtbWF4LXZhbHVlJyApICk7XG5cdFx0XHRcdGNvbnN0IGRlY2ltYWxQbGFjZXMgICAgID0gJHJhbmdlTnVtYmVyLmF0dHIoICdkYXRhLWRlY2ltYWwtcGxhY2VzJyApO1xuXHRcdFx0XHRjb25zdCB0aG91c2FuZFNlcGFyYXRvciA9ICRyYW5nZU51bWJlci5hdHRyKCAnZGF0YS10aG91c2FuZC1zZXBhcmF0b3InICk7XG5cdFx0XHRcdGNvbnN0IGRlY2ltYWxTZXBhcmF0b3IgID0gJHJhbmdlTnVtYmVyLmF0dHIoICdkYXRhLWRlY2ltYWwtc2VwYXJhdG9yJyApO1xuXG5cdFx0XHRcdC8vIENsZWFyIGFueSBwcmV2aW91c2x5IHNldCB0aW1lciBiZWZvcmUgc2V0dGluZyBhIGZyZXNoIG9uZVxuXHRcdFx0XHRjbGVhclRpbWVvdXQoICRpdGVtLmRhdGEoICd0aW1lcicgKSApO1xuXG5cdFx0XHRcdGNvbnN0IGdldFZhbHVlID0gKCBmbG9hdFZhbHVlICkgPT4ge1xuXHRcdFx0XHRcdGlmICggZm9ybWF0TnVtYmVycyApIHtcblx0XHRcdFx0XHRcdHJldHVybiBudW1iZXJGb3JtYXQoIGZsb2F0VmFsdWUsIGRlY2ltYWxQbGFjZXMsIGRlY2ltYWxTZXBhcmF0b3IsIHRob3VzYW5kU2VwYXJhdG9yICk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0cmV0dXJuIGZsb2F0VmFsdWU7XG5cdFx0XHRcdH07XG5cblx0XHRcdFx0JGl0ZW0uZGF0YSggJ3RpbWVyJywgc2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JGl0ZW0ucmVtb3ZlRGF0YSggJ3RpbWVyJyApO1xuXG5cdFx0XHRcdFx0bGV0IG1pblZhbHVlID0gcGFyc2VGbG9hdCggJHJhbmdlTnVtYmVyLmZpbmQoICcubWluLXZhbHVlJyApLnZhbCgpICk7XG5cdFx0XHRcdFx0bGV0IG1heFZhbHVlID0gcGFyc2VGbG9hdCggJHJhbmdlTnVtYmVyLmZpbmQoICcubWF4LXZhbHVlJyApLnZhbCgpICk7XG5cblx0XHRcdFx0XHQvLyBGb3JjZSB0aGUgbWluVmFsdWUgbm90IHRvIGJlIGVtcHR5LlxuXHRcdFx0XHRcdGlmICggaXNOYU4oIG1pblZhbHVlICkgKSB7XG5cdFx0XHRcdFx0XHRtaW5WYWx1ZSA9IHJhbmdlTWluVmFsdWU7XG5cblx0XHRcdFx0XHRcdCRyYW5nZU51bWJlci5maW5kKCAnLm1pbi12YWx1ZScgKS52YWwoIGdldFZhbHVlKCBtaW5WYWx1ZSApICk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdCRyYW5nZU51bWJlci5maW5kKCAnLm1pbi12YWx1ZScgKS52YWwoIGdldFZhbHVlKCBtaW5WYWx1ZSApICk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gRm9yY2UgdGhlIG1heFZhbHVlIG5vdCB0byBiZSBlbXB0eS5cblx0XHRcdFx0XHRpZiAoIGlzTmFOKCBtYXhWYWx1ZSApICkge1xuXHRcdFx0XHRcdFx0bWF4VmFsdWUgPSByYW5nZU1heFZhbHVlO1xuXG5cdFx0XHRcdFx0XHQkcmFuZ2VOdW1iZXIuZmluZCggJy5tYXgtdmFsdWUnICkudmFsKCBnZXRWYWx1ZSggbWF4VmFsdWUgKSApO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHQkcmFuZ2VOdW1iZXIuZmluZCggJy5tYXgtdmFsdWUnICkudmFsKCBnZXRWYWx1ZSggbWF4VmFsdWUgKSApO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEZvcmNlIHRoZSBtaW5WYWx1ZSBub3QgdG8gZ28gYmVsb3cgdGhlIHJhbmdlTWluVmFsdWUuXG5cdFx0XHRcdFx0aWYgKCBtaW5WYWx1ZSA8IHJhbmdlTWluVmFsdWUgKSB7XG5cdFx0XHRcdFx0XHRtaW5WYWx1ZSA9IHJhbmdlTWluVmFsdWU7XG5cblx0XHRcdFx0XHRcdCRyYW5nZU51bWJlci5maW5kKCAnLm1pbi12YWx1ZScgKS52YWwoIGdldFZhbHVlKCBtaW5WYWx1ZSApICk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gRm9yY2UgdGhlIG1pblZhbHVlIG5vdCB0byBnbyB1cCB0aGUgcmFuZ2VNYXhWYWx1ZS5cblx0XHRcdFx0XHRpZiAoIG1pblZhbHVlID4gcmFuZ2VNYXhWYWx1ZSApIHtcblx0XHRcdFx0XHRcdG1pblZhbHVlID0gcmFuZ2VNYXhWYWx1ZTtcblxuXHRcdFx0XHRcdFx0JHJhbmdlTnVtYmVyLmZpbmQoICcubWluLXZhbHVlJyApLnZhbCggZ2V0VmFsdWUoIG1pblZhbHVlICkgKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvLyBGb3JjZSB0aGUgbWF4VmFsdWUgbm90IHRvIGdvIHVwIHRoZSByYW5nZU1heFZhbHVlLlxuXHRcdFx0XHRcdGlmICggbWF4VmFsdWUgPiByYW5nZU1heFZhbHVlICkge1xuXHRcdFx0XHRcdFx0bWF4VmFsdWUgPSByYW5nZU1heFZhbHVlO1xuXG5cdFx0XHRcdFx0XHQkcmFuZ2VOdW1iZXIuZmluZCggJy5tYXgtdmFsdWUnICkudmFsKCBnZXRWYWx1ZSggbWF4VmFsdWUgKSApO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEZvcmNlIHRoZSBtYXhWYWx1ZSBub3QgdG8gZ28gYmVsb3cgdGhlIG1pblZhbHVlLlxuXHRcdFx0XHRcdGlmICggbWluVmFsdWUgPiBtYXhWYWx1ZSApIHtcblx0XHRcdFx0XHRcdG1heFZhbHVlID0gbWluVmFsdWU7XG5cblx0XHRcdFx0XHRcdCRyYW5nZU51bWJlci5maW5kKCAnLm1heC12YWx1ZScgKS52YWwoIGdldFZhbHVlKCBtYXhWYWx1ZSApICk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gSWYgdmFsdWUgaXMgbm90IGNoYW5nZWQgdGhlbiBkb24ndCBwcm9jZWVkLlxuXHRcdFx0XHRcdGlmICggbWluVmFsdWUgPT09IG9sZE1pblZhbHVlICYmIG1heFZhbHVlID09PSBvbGRNYXhWYWx1ZSApIHtcblx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRpZiAoIG1pblZhbHVlID09PSByYW5nZU1pblZhbHVlICYmIG1heFZhbHVlID09PSByYW5nZU1heFZhbHVlICkge1xuXHRcdFx0XHRcdFx0Ly8gUmVtb3ZlIHJhbmdlIGZpbHRlci5cblx0XHRcdFx0XHRcdFdDQVBGLnJlcXVlc3RGaWx0ZXIoICRyYW5nZU51bWJlci5kYXRhKCAnY2xlYXItZmlsdGVyLXVybCcgKSApO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHQvLyBBZGQgcmFuZ2UgZmlsdGVyLlxuXHRcdFx0XHRcdFx0Y29uc3QgdXJsID0gJHJhbmdlTnVtYmVyLmRhdGEoICd1cmwnICkucmVwbGFjZSggJyUxcycsIG1pblZhbHVlICkucmVwbGFjZSggJyUycycsIG1heFZhbHVlICk7XG5cdFx0XHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCB1cmwgKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0sIGRlbGF5ICkgKTtcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZURhdGVJbnB1dEZpbHRlcnM6IGZ1bmN0aW9uKCkge1xuXHRcdFx0JGJvZHkub24oICdjaGFuZ2UnLCAnLndjYXBmLWRhdGUtaW5wdXQgLmRhdGUtaW5wdXQnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3QgJGZpbHRlciA9ICQoIHRoaXMgKS5jbG9zZXN0KCAnLndjYXBmLWRhdGUtaW5wdXQnICk7XG5cdFx0XHRcdGNvbnN0IGlzUmFuZ2UgPSAkZmlsdGVyLmRhdGEoICdpcy1yYW5nZScgKTtcblxuXHRcdFx0XHRsZXQgZmlsdGVyVXJsID0gJyc7XG5cblx0XHRcdFx0Ly8gQ2xlYXIgYW55IHByZXZpb3VzbHkgc2V0IHRpbWVyIGJlZm9yZSBzZXR0aW5nIGEgZnJlc2ggb25lXG5cdFx0XHRcdGNsZWFyVGltZW91dCggJGZpbHRlci5kYXRhKCAndGltZXInICkgKTtcblxuXHRcdFx0XHRpZiAoIGlzUmFuZ2UgKSB7XG5cdFx0XHRcdFx0Y29uc3QgZnJvbSA9ICRmaWx0ZXIuZmluZCggJy5kYXRlLWZyb20taW5wdXQnICkudmFsKCk7XG5cdFx0XHRcdFx0Y29uc3QgdG8gICA9ICRmaWx0ZXIuZmluZCggJy5kYXRlLXRvLWlucHV0JyApLnZhbCgpO1xuXG5cdFx0XHRcdFx0aWYgKCBmcm9tICYmIHRvICkge1xuXHRcdFx0XHRcdFx0ZmlsdGVyVXJsID0gJGZpbHRlci5kYXRhKCAndXJsJyApLnJlcGxhY2UoICclMXMnLCBmcm9tICkucmVwbGFjZSggJyUycycsIHRvICk7XG5cdFx0XHRcdFx0fSBlbHNlIGlmICggISBmcm9tICYmICEgdG8gKSB7XG5cdFx0XHRcdFx0XHRmaWx0ZXJVcmwgPSAkZmlsdGVyLmRhdGEoICdjbGVhci1maWx0ZXItdXJsJyApO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRjb25zdCBmcm9tID0gJGZpbHRlci5maW5kKCAnLmRhdGUtZnJvbS1pbnB1dCcgKS52YWwoKTtcblxuXHRcdFx0XHRcdGlmICggZnJvbSApIHtcblx0XHRcdFx0XHRcdGZpbHRlclVybCA9ICRmaWx0ZXIuZGF0YSggJ3VybCcgKS5yZXBsYWNlKCAnJXMnLCBmcm9tICk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdGZpbHRlclVybCA9ICRmaWx0ZXIuZGF0YSggJ2NsZWFyLWZpbHRlci11cmwnICk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cblx0XHRcdFx0aWYgKCBmaWx0ZXJVcmwgKSB7XG5cdFx0XHRcdFx0JGZpbHRlci5kYXRhKCAndGltZXInLCBzZXRUaW1lb3V0KCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCRmaWx0ZXIucmVtb3ZlRGF0YSggJ3RpbWVyJyApO1xuXG5cdFx0XHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCBmaWx0ZXJVcmwgKTtcblx0XHRcdFx0XHR9LCBkZWxheSApICk7XG5cdFx0XHRcdH1cblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZUxpc3RGaWx0ZXJzOiBmdW5jdGlvbigpIHtcblx0XHRcdGNvbnN0IG5hdGl2ZUlucHV0cyA9ICcubGlzdC10eXBlLW5hdGl2ZSBbdHlwZT1cImNoZWNrYm94XCJdLCcgK1xuXHRcdFx0XHQnLmxpc3QtdHlwZS1uYXRpdmUgW3R5cGU9XCJyYWRpb1wiXSwnICtcblx0XHRcdFx0Jy5saXN0LXR5cGUtY3VzdG9tLWNoZWNrYm94IFt0eXBlPVwiY2hlY2tib3hcIl0nO1xuXG5cdFx0XHQkYm9keS5vbiggJ2NoYW5nZScsIG5hdGl2ZUlucHV0cywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQoIHRoaXMgKS5jbG9zZXN0KCAnLndjYXBmLWZpbHRlci1pdGVtJyApLnRvZ2dsZUNsYXNzKCAnaXRlbS1hY3RpdmUnICk7XG5cblx0XHRcdFx0V0NBUEYucmVxdWVzdEZpbHRlciggJCggdGhpcyApLmRhdGEoICd1cmwnICkgKTtcblx0XHRcdH0gKTtcblxuXHRcdFx0Y29uc3QgY3VzdG9tUmFkaW9TZWxlY3RvciA9ICcubGlzdC10eXBlLWN1c3RvbS1yYWRpbyc7XG5cblx0XHRcdCRib2R5Lm9uKCAnY2hhbmdlJywgY3VzdG9tUmFkaW9TZWxlY3RvciArICcgW3R5cGU9XCJjaGVja2JveFwiXScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkKCB0aGlzICkuY2xvc2VzdCggJy53Y2FwZi1maWx0ZXItaXRlbScgKS50b2dnbGVDbGFzcyggJ2l0ZW0tYWN0aXZlJyApO1xuXG5cdFx0XHRcdC8vIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vYS81ODM5OTI0XG5cdFx0XHRcdCQoIHRoaXMgKVxuXHRcdFx0XHRcdC5jbG9zZXN0KCBjdXN0b21SYWRpb1NlbGVjdG9yIClcblx0XHRcdFx0XHQuZmluZCggJy53Y2FwZi1maWx0ZXItaXRlbS5pdGVtLWFjdGl2ZSBbdHlwZT1cImNoZWNrYm94XCJdJyApXG5cdFx0XHRcdFx0Lm5vdCggdGhpcyApXG5cdFx0XHRcdFx0LnByb3AoICdjaGVja2VkJywgZmFsc2UgKVxuXHRcdFx0XHRcdC5jbG9zZXN0KCAnLndjYXBmLWZpbHRlci1pdGVtJyApXG5cdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCAnaXRlbS1hY3RpdmUnICk7XG5cblx0XHRcdFx0V0NBUEYucmVxdWVzdEZpbHRlciggJCggdGhpcyApLmRhdGEoICd1cmwnICkgKTtcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZURyb3Bkb3duRmlsdGVyczogZnVuY3Rpb24oKSB7XG5cdFx0XHQkYm9keS5vbiggJ2NoYW5nZScsICcud2NhcGYtZHJvcGRvd24td3JhcHBlciBzZWxlY3QnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3QgJHNlbGVjdCAgICAgICAgPSAkKCB0aGlzICk7XG5cdFx0XHRcdGNvbnN0IHZhbHVlcyAgICAgICAgID0gJHNlbGVjdC52YWwoKTtcblx0XHRcdFx0Y29uc3QgZmlsdGVyVVJMICAgICAgPSAkc2VsZWN0LmRhdGEoICd1cmwnICk7XG5cdFx0XHRcdGNvbnN0IGNsZWFyRmlsdGVyVVJMID0gJHNlbGVjdC5kYXRhKCAnY2xlYXItZmlsdGVyLXVybCcgKTtcblx0XHRcdFx0bGV0IHVybDtcblxuXHRcdFx0XHRpZiAoIHZhbHVlcy5sZW5ndGggKSB7XG5cdFx0XHRcdFx0dXJsID0gZmlsdGVyVVJMLnJlcGxhY2UoICclcycsIHZhbHVlcy50b1N0cmluZygpICk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0dXJsID0gY2xlYXJGaWx0ZXJVUkw7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCB1cmwgKTtcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZVBhZ2luYXRpb246IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMuZW5hYmxlX3BhZ2luYXRpb25fdmlhX2FqYXggJiYgd2NhcGZfcGFyYW1zLnBhZ2luYXRpb25fY29udGFpbmVyICkge1xuXHRcdFx0XHRjb25zdCAkY29udGFpbmVyID0gJCggd2NhcGZfcGFyYW1zLnNob3BfbG9vcF9jb250YWluZXIgKTtcblx0XHRcdFx0Y29uc3QgX3NlbGVjdG9ycyA9IHdjYXBmX3BhcmFtcy5wYWdpbmF0aW9uX2NvbnRhaW5lci5zcGxpdCggJywnICk7XG5cdFx0XHRcdGNvbnN0IHNlbGVjdG9ycyAgPSBbXTtcblxuXHRcdFx0XHRfc2VsZWN0b3JzLmZvckVhY2goIHNlbGVjdG9yID0+IHtcblx0XHRcdFx0XHRpZiAoIHNlbGVjdG9yICkge1xuXHRcdFx0XHRcdFx0c2VsZWN0b3JzLnB1c2goIHNlbGVjdG9yICsgJyBhJyApO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSApO1xuXG5cdFx0XHRcdGNvbnN0IHNlbGVjdG9yID0gc2VsZWN0b3JzLmpvaW4oICcsJyApO1xuXG5cdFx0XHRcdGlmICggJGNvbnRhaW5lci5sZW5ndGggKSB7XG5cdFx0XHRcdFx0JGNvbnRhaW5lci5vbiggJ2NsaWNrJywgc2VsZWN0b3IsIGZ1bmN0aW9uKCBlICkge1xuXHRcdFx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHRcdFx0XHRjb25zdCBocmVmID0gJCggdGhpcyApLmF0dHIoICdocmVmJyApO1xuXG5cdFx0XHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCBocmVmLCAncGFnaW5hdGUnICk7XG5cdFx0XHRcdFx0fSApO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fSxcblx0XHRoYW5kbGVEZWZhdWx0T3JkZXJieTogZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoICEgd2NhcGZfcGFyYW1zLnNvcnRpbmdfY29udHJvbCApIHtcblx0XHRcdFx0Ly8gU3VibWl0IHRoZSBvcmRlcmJ5IGZvcm0gd2hlbiB2YWx1ZSBpcyBjaGFuZ2VkLlxuXHRcdFx0XHQkYm9keS5vbiggJ2NoYW5nZScsIGRlZmF1bHRPcmRlckJ5RWxlbWVudCwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCggdGhpcyApLmNsb3Nlc3QoICdmb3JtJyApLnRyaWdnZXIoICdzdWJtaXQnICk7XG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8vIFByZXZlbnQgdGhlIGF1dG8gc3VibWlzc2lvbiBvZiB0aGUgb3JkZXJieSBmb3JtLlxuXHRcdFx0JGJvZHkub24oICdzdWJtaXQnLCB3Y2FwZl9wYXJhbXMub3JkZXJieV9mb3JtLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fSApO1xuXG5cdFx0XHQvLyBIYW5kbGUgdGhlIGZpbHRlciByZXF1ZXN0IHZpYSBhamF4IHdoZW4gdGhlIG9yZGVyYnkgdmFsdWUgaXMgY2hhbmdlZC5cblx0XHRcdCRib2R5Lm9uKCAnY2hhbmdlJywgZGVmYXVsdE9yZGVyQnlFbGVtZW50LCBmdW5jdGlvbigpIHtcblx0XHRcdFx0Y29uc3Qgb3JkZXIgPSAkKCB0aGlzICkudmFsKCk7XG5cblx0XHRcdFx0Y29uc3QgdXJsID0gbmV3IFVSTCggd2luZG93LmxvY2F0aW9uICk7XG5cdFx0XHRcdHVybC5zZWFyY2hQYXJhbXMuc2V0KCAnb3JkZXJieScsIG9yZGVyICk7XG5cblx0XHRcdFx0V0NBUEYucmVxdWVzdEZpbHRlciggZ2V0T3JkZXJCeVVybCggdXJsLmhyZWYgKSApO1xuXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGhhbmRsZUNsZWFyRmlsdGVyOiBmdW5jdGlvbigpIHtcblx0XHRcdCRib2R5Lm9uKCAnY2xpY2snLCAnLndjYXBmLWZpbHRlci1jbGVhci1idG4nLCBmdW5jdGlvbiggZSApIHtcblx0XHRcdFx0ZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCAkKCB0aGlzICkuYXR0ciggJ2RhdGEtY2xlYXItZmlsdGVyLXVybCcgKSApO1xuXHRcdFx0fSApO1xuXHRcdH0sXG5cdFx0aGFuZGxlRmlsdGVyVG9vbHRpcDogZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBub2luc3BlY3Rpb24gSlNVbnJlc29sdmVkUmVmZXJlbmNlXG5cdFx0XHRpZiAoICdmdW5jdGlvbicgIT09IHR5cGVvZiB0aXBweSApIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHRpZiAoICEgd2NhcGZfcGFyYW1zLnVzZV90aXBweWpzICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8vIG5vaW5zcGVjdGlvbiBKU1VucmVzb2x2ZWRSZWZlcmVuY2Vcblx0XHRcdHRpcHB5KCAnLndjYXBmLWZpbHRlci10b29sdGlwJywge1xuXHRcdFx0XHRwbGFjZW1lbnQ6ICd0b3AnLFxuXHRcdFx0XHRjb250ZW50KCByZWZlcmVuY2UgKSB7XG5cdFx0XHRcdFx0cmV0dXJuIHJlZmVyZW5jZS5nZXRBdHRyaWJ1dGUoICdkYXRhLWNvbnRlbnQnICk7XG5cdFx0XHRcdH0sXG5cdFx0XHRcdGFsbG93SFRNTDogdHJ1ZSxcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGluaXRDb21ib2JveDogZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoICEgalF1ZXJ5KCkuY2hvc2VuV0NBUEYgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0Y29uc3QgdGVtcGxhdGVSZXN1bHQgPSAoIHRleHQsIGRhdGEgKSA9PiB7XG5cdFx0XHRcdHJldHVybiBbXG5cdFx0XHRcdFx0JzxzcGFuPicgKyB0ZXh0ICsgJzwvc3Bhbj4nLFxuXHRcdFx0XHRcdCc8c3BhbiBjbGFzcz1cIndjYXBmLWNvdW50XCI+JyArIGRhdGFbICdjb3VudE1hcmt1cCcgXSArICc8L3NwYW4+Jyxcblx0XHRcdFx0XS5qb2luKCAnJyApO1xuXHRcdFx0fTtcblxuXHRcdFx0Y29uc3QgdGVtcGxhdGVTZWxlY3Rpb24gPSAoIHRleHQsIGRhdGEgKSA9PiB7XG5cdFx0XHRcdHJldHVybiBbXG5cdFx0XHRcdFx0JzxzcGFuIGNsYXNzPVwid2NhcGYtY291bnQtJyArIGRhdGEuY291bnQgKyAnXCI+JyArIHRleHQgKyAnPC9zcGFuPicsXG5cdFx0XHRcdFx0JzxzcGFuIGNsYXNzPVwid2NhcGYtY291bnQgd2NhcGYtY291bnQtJyArIGRhdGEuY291bnQgKyAnXCI+JyArIGRhdGFbICdjb3VudE1hcmt1cCcgXSArICc8L3NwYW4+Jyxcblx0XHRcdFx0XS5qb2luKCAnJyApO1xuXHRcdFx0fTtcblxuXHRcdFx0Y29uc3QgZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGluaGVyaXRfc2VsZWN0X2NsYXNzZXM6IHRydWUsXG5cdFx0XHRcdGluaGVyaXRfb3B0aW9uX2NsYXNzZXM6IHRydWUsXG5cdFx0XHRcdG5vX3Jlc3VsdHNfdGV4dDogd2NhcGZfcGFyYW1zLmNvbWJvYm94X25vX3Jlc3VsdHNfdGV4dCxcblx0XHRcdFx0b3B0aW9uc19ub25lX3RleHQ6IHdjYXBmX3BhcmFtcy5jb21ib2JveF9vcHRpb25zX25vbmVfdGV4dCxcblx0XHRcdFx0c2VhcmNoX2NvbnRhaW5zOiB0cnVlLCAvLyBNYXRjaCBmcm9tIGFueXdoZXJlIGluIHN0cmluZy5cblx0XHRcdFx0c2VhcmNoX2luX3ZhbHVlczogdHJ1ZSwgLy8gU2VhcmNoIGluIHZhbHVlcyBhbHNvLlxuXHRcdFx0fTtcblxuXHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMuaXNfcnRsICkge1xuXHRcdFx0XHRkZWZhdWx0c1sgJ3J0bCcgXSA9IHRydWU7XG5cdFx0XHR9XG5cblx0XHRcdCRib2R5LmZpbmQoICcud2NhcGYtY2hvc2VuJyApLmVhY2goIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRjb25zdCAkdGhpcyAgID0gJCggdGhpcyApO1xuXHRcdFx0XHRjb25zdCBvcHRpb25zID0geyAuLi5kZWZhdWx0cyB9O1xuXG5cdFx0XHRcdC8vIElmIGhpZXJhcmNoeSBlbmFibGVkIHRoZW4gd2Ugc2hvdyB0aGUgc2VsZWN0ZWQgb3B0aW9ucy5cblx0XHRcdFx0aWYgKCAkdGhpcy5oYXNDbGFzcyggJ2hhcy1oaWVyYXJjaHknICkgKSB7XG5cdFx0XHRcdFx0b3B0aW9uc1sgJ2Rpc3BsYXlfc2VsZWN0ZWRfb3B0aW9ucycgXSA9IHRydWU7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0b3B0aW9uc1sgJ2Rpc3BsYXlfc2VsZWN0ZWRfb3B0aW9ucycgXSA9IHdjYXBmX3BhcmFtcy5jb21ib2JveF9kaXNwbGF5X3NlbGVjdGVkX29wdGlvbnM7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHQvLyBFbmFibGUgdGVtcGxhdGluZyB3aGVuIHNob3dpbmcgY291bnQuXG5cdFx0XHRcdGlmICggJHRoaXMuaGFzQ2xhc3MoICd3aXRoLWNvdW50JyApICkge1xuXHRcdFx0XHRcdG9wdGlvbnNbICd0ZW1wbGF0ZVJlc3VsdCcgXSAgICA9IHRlbXBsYXRlUmVzdWx0O1xuXHRcdFx0XHRcdG9wdGlvbnNbICd0ZW1wbGF0ZVNlbGVjdGlvbicgXSA9IHRlbXBsYXRlU2VsZWN0aW9uO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Ly8gRGlzYWJsZSBzZWFyY2ggYm94LlxuXHRcdFx0XHRpZiAoICEgJHRoaXMuZGF0YSggJ2VuYWJsZS1zZWFyY2gnICkgKSB7XG5cdFx0XHRcdFx0b3B0aW9uc1sgJ2Rpc2FibGVfc2VhcmNoJyBdID0gdHJ1ZTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdCR0aGlzLmNob3NlbldDQVBGKCBvcHRpb25zICk7XG5cdFx0XHR9ICk7XG5cblx0XHRcdC8vIEF0dGFjaCBjaG9zZW4gZm9yIGRlZmF1bHQgb3JkZXJieS5cblx0XHRcdGlmICggd2NhcGZfcGFyYW1zLmF0dGFjaF9jb21ib2JveF9vbl9zb3J0aW5nICkge1xuXHRcdFx0XHRsZXQgZGlzYWJsZVNlYXJjaCA9IHRydWU7XG5cblx0XHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMuc2VhcmNoX2JveF9pbl9kZWZhdWx0X29yZGVyYnkgKSB7XG5cdFx0XHRcdFx0ZGlzYWJsZVNlYXJjaCA9IGZhbHNlO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Y29uc3Qgb3B0aW9ucyA9IHsgLi4uZGVmYXVsdHMgfTtcblxuXHRcdFx0XHRvcHRpb25zWyAnZGlzYWJsZV9zZWFyY2gnIF0gPSBkaXNhYmxlU2VhcmNoO1xuXG5cdFx0XHRcdCRib2R5LmZpbmQoIGRlZmF1bHRPcmRlckJ5RWxlbWVudCApLmNob3NlbldDQVBGKCBvcHRpb25zICk7XG5cdFx0XHR9XG5cdFx0fSxcblx0XHRpbml0UmFuZ2VTbGlkZXI6IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIG5vVWlTbGlkZXIgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0JGJvZHkuZmluZCggJy53Y2FwZi1yYW5nZS1zbGlkZXInICkuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGNvbnN0ICRpdGVtICAgPSAkKCB0aGlzICk7XG5cdFx0XHRcdGNvbnN0ICRzbGlkZXIgPSAkaXRlbS5maW5kKCAnLndjYXBmLW5vdWktc2xpZGVyJyApO1xuXG5cdFx0XHRcdGNvbnN0IHNsaWRlcklkICAgICAgICAgID0gJHNsaWRlci5hdHRyKCAnaWQnICk7XG5cdFx0XHRcdGNvbnN0IGRpc3BsYXlWYWx1ZXNBcyAgID0gJGl0ZW0uYXR0ciggJ2RhdGEtZGlzcGxheS12YWx1ZXMtYXMnICk7XG5cdFx0XHRcdGNvbnN0IGZvcm1hdE51bWJlcnMgICAgID0gJGl0ZW0uYXR0ciggJ2RhdGEtZm9ybWF0LW51bWJlcnMnICk7XG5cdFx0XHRcdGNvbnN0IHJhbmdlTWluVmFsdWUgICAgID0gcGFyc2VGbG9hdCggJGl0ZW0uYXR0ciggJ2RhdGEtcmFuZ2UtbWluLXZhbHVlJyApICk7XG5cdFx0XHRcdGNvbnN0IHJhbmdlTWF4VmFsdWUgICAgID0gcGFyc2VGbG9hdCggJGl0ZW0uYXR0ciggJ2RhdGEtcmFuZ2UtbWF4LXZhbHVlJyApICk7XG5cdFx0XHRcdGNvbnN0IHN0ZXAgICAgICAgICAgICAgID0gcGFyc2VGbG9hdCggJGl0ZW0uYXR0ciggJ2RhdGEtc3RlcCcgKSApO1xuXHRcdFx0XHRjb25zdCBkZWNpbWFsUGxhY2VzICAgICA9ICRpdGVtLmF0dHIoICdkYXRhLWRlY2ltYWwtcGxhY2VzJyApO1xuXHRcdFx0XHRjb25zdCB0aG91c2FuZFNlcGFyYXRvciA9ICRpdGVtLmF0dHIoICdkYXRhLXRob3VzYW5kLXNlcGFyYXRvcicgKTtcblx0XHRcdFx0Y29uc3QgZGVjaW1hbFNlcGFyYXRvciAgPSAkaXRlbS5hdHRyKCAnZGF0YS1kZWNpbWFsLXNlcGFyYXRvcicgKTtcblx0XHRcdFx0Y29uc3QgbWluVmFsdWUgICAgICAgICAgPSBwYXJzZUZsb2F0KCAkaXRlbS5hdHRyKCAnZGF0YS1taW4tdmFsdWUnICkgKTtcblx0XHRcdFx0Y29uc3QgbWF4VmFsdWUgICAgICAgICAgPSBwYXJzZUZsb2F0KCAkaXRlbS5hdHRyKCAnZGF0YS1tYXgtdmFsdWUnICkgKTtcblx0XHRcdFx0Y29uc3QgJG1pblZhbHVlICAgICAgICAgPSAkaXRlbS5maW5kKCAnLm1pbi12YWx1ZScgKTtcblx0XHRcdFx0Y29uc3QgJG1heFZhbHVlICAgICAgICAgPSAkaXRlbS5maW5kKCAnLm1heC12YWx1ZScgKTtcblxuXHRcdFx0XHRjb25zdCBzbGlkZXIgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggc2xpZGVySWQgKTtcblxuXHRcdFx0XHRub1VpU2xpZGVyLmNyZWF0ZSggc2xpZGVyLCB7XG5cdFx0XHRcdFx0c3RhcnQ6IFsgbWluVmFsdWUsIG1heFZhbHVlIF0sXG5cdFx0XHRcdFx0c3RlcCxcblx0XHRcdFx0XHRjb25uZWN0OiB0cnVlLFxuXHRcdFx0XHRcdGNzc1ByZWZpeDogJ3djYXBmLW5vdWktJyxcblx0XHRcdFx0XHRyYW5nZToge1xuXHRcdFx0XHRcdFx0J21pbic6IHJhbmdlTWluVmFsdWUsXG5cdFx0XHRcdFx0XHQnbWF4JzogcmFuZ2VNYXhWYWx1ZSxcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRzbGlkZXIubm9VaVNsaWRlci5vbiggJ3VwZGF0ZScsIGZ1bmN0aW9uKCB2YWx1ZXMgKSB7XG5cdFx0XHRcdFx0bGV0IG1pblZhbHVlO1xuXHRcdFx0XHRcdGxldCBtYXhWYWx1ZTtcblxuXHRcdFx0XHRcdGlmICggZm9ybWF0TnVtYmVycyApIHtcblx0XHRcdFx0XHRcdG1pblZhbHVlID0gbnVtYmVyRm9ybWF0KCB2YWx1ZXNbIDAgXSwgZGVjaW1hbFBsYWNlcywgZGVjaW1hbFNlcGFyYXRvciwgdGhvdXNhbmRTZXBhcmF0b3IgKTtcblx0XHRcdFx0XHRcdG1heFZhbHVlID0gbnVtYmVyRm9ybWF0KCB2YWx1ZXNbIDEgXSwgZGVjaW1hbFBsYWNlcywgZGVjaW1hbFNlcGFyYXRvciwgdGhvdXNhbmRTZXBhcmF0b3IgKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0bWluVmFsdWUgPSBwYXJzZUZsb2F0KCB2YWx1ZXNbIDAgXSApO1xuXHRcdFx0XHRcdFx0bWF4VmFsdWUgPSBwYXJzZUZsb2F0KCB2YWx1ZXNbIDEgXSApO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGlmICggJ3BsYWluX3RleHQnID09PSBkaXNwbGF5VmFsdWVzQXMgKSB7XG5cdFx0XHRcdFx0XHQkbWluVmFsdWUuaHRtbCggbWluVmFsdWUgKTtcblx0XHRcdFx0XHRcdCRtYXhWYWx1ZS5odG1sKCBtYXhWYWx1ZSApO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHQkbWluVmFsdWUudmFsKCBtaW5WYWx1ZSApO1xuXHRcdFx0XHRcdFx0JG1heFZhbHVlLnZhbCggbWF4VmFsdWUgKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRmdW5jdGlvbiBmaWx0ZXJQcm9kdWN0c0FjY29yZGluZ1RvU2xpZGVyKCB2YWx1ZXMgKSB7XG5cdFx0XHRcdFx0Y29uc3QgX21pblZhbHVlID0gcGFyc2VGbG9hdCggdmFsdWVzWyAwIF0gKTtcblx0XHRcdFx0XHRjb25zdCBfbWF4VmFsdWUgPSBwYXJzZUZsb2F0KCB2YWx1ZXNbIDEgXSApO1xuXG5cdFx0XHRcdFx0Ly8gSWYgdmFsdWUgaXMgbm90IGNoYW5nZWQgdGhlbiBkb24ndCBwcm9jZWVkLlxuXHRcdFx0XHRcdGlmICggX21pblZhbHVlID09PSBtaW5WYWx1ZSAmJiBfbWF4VmFsdWUgPT09IG1heFZhbHVlICkge1xuXHRcdFx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGlmICggX21pblZhbHVlID09PSByYW5nZU1pblZhbHVlICYmIF9tYXhWYWx1ZSA9PT0gcmFuZ2VNYXhWYWx1ZSApIHtcblx0XHRcdFx0XHRcdC8vIFJlbW92ZSByYW5nZSBmaWx0ZXIuXG5cdFx0XHRcdFx0XHRXQ0FQRi5yZXF1ZXN0RmlsdGVyKCAkaXRlbS5kYXRhKCAnY2xlYXItZmlsdGVyLXVybCcgKSApO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHQvLyBBZGQgcmFuZ2UgZmlsdGVyLlxuXHRcdFx0XHRcdFx0Y29uc3QgdXJsID0gJGl0ZW0uZGF0YSggJ3VybCcgKS5yZXBsYWNlKCAnJTFzJywgX21pblZhbHVlICkucmVwbGFjZSggJyUycycsIF9tYXhWYWx1ZSApO1xuXHRcdFx0XHRcdFx0V0NBUEYucmVxdWVzdEZpbHRlciggdXJsICk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cblx0XHRcdFx0c2xpZGVyLm5vVWlTbGlkZXIub24oICdjaGFuZ2UnLCBmdW5jdGlvbiggdmFsdWVzICkge1xuXHRcdFx0XHRcdC8vIENsZWFyIGFueSBwcmV2aW91c2x5IHNldCB0aW1lciBiZWZvcmUgc2V0dGluZyBhIGZyZXNoIG9uZVxuXHRcdFx0XHRcdGNsZWFyVGltZW91dCggJGl0ZW0uZGF0YSggJ3RpbWVyJyApICk7XG5cblx0XHRcdFx0XHQkaXRlbS5kYXRhKCAndGltZXInLCBzZXRUaW1lb3V0KCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCRpdGVtLnJlbW92ZURhdGEoICd0aW1lcicgKTtcblxuXHRcdFx0XHRcdFx0ZmlsdGVyUHJvZHVjdHNBY2NvcmRpbmdUb1NsaWRlciggdmFsdWVzICk7XG5cdFx0XHRcdFx0fSwgZGVsYXkgKSApO1xuXHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0JG1pblZhbHVlLm9uKCAnaW5wdXQnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRjb25zdCAkaW5wdXQgPSAkKCB0aGlzICk7XG5cblx0XHRcdFx0XHQvLyBDbGVhciBhbnkgcHJldmlvdXNseSBzZXQgdGltZXIgYmVmb3JlIHNldHRpbmcgYSBmcmVzaCBvbmVcblx0XHRcdFx0XHRjbGVhclRpbWVvdXQoICRpbnB1dC5kYXRhKCAndGltZXInICkgKTtcblxuXHRcdFx0XHRcdCRpbnB1dC5kYXRhKCAndGltZXInLCBzZXRUaW1lb3V0KCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCRpbnB1dC5yZW1vdmVEYXRhKCAndGltZXInICk7XG5cblx0XHRcdFx0XHRcdGNvbnN0IG1pblZhbHVlID0gJGlucHV0LnZhbCgpO1xuXG5cdFx0XHRcdFx0XHRzbGlkZXIubm9VaVNsaWRlci5zZXQoIFsgbWluVmFsdWUsIG51bGwgXSApO1xuXG5cdFx0XHRcdFx0XHRmaWx0ZXJQcm9kdWN0c0FjY29yZGluZ1RvU2xpZGVyKCBzbGlkZXIubm9VaVNsaWRlci5nZXQoKSApO1xuXHRcdFx0XHRcdH0sIGRlbGF5ICkgKTtcblx0XHRcdFx0fSApO1xuXG5cdFx0XHRcdCRtYXhWYWx1ZS5vbiggJ2lucHV0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0Y29uc3QgJGlucHV0ID0gJCggdGhpcyApO1xuXG5cdFx0XHRcdFx0Ly8gQ2xlYXIgYW55IHByZXZpb3VzbHkgc2V0IHRpbWVyIGJlZm9yZSBzZXR0aW5nIGEgZnJlc2ggb25lXG5cdFx0XHRcdFx0Y2xlYXJUaW1lb3V0KCAkaW5wdXQuZGF0YSggJ3RpbWVyJyApICk7XG5cblx0XHRcdFx0XHQkaW5wdXQuZGF0YSggJ3RpbWVyJywgc2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHQkaW5wdXQucmVtb3ZlRGF0YSggJ3RpbWVyJyApO1xuXG5cdFx0XHRcdFx0XHRjb25zdCBtYXhWYWx1ZSA9ICRpbnB1dC52YWwoKTtcblxuXHRcdFx0XHRcdFx0c2xpZGVyLm5vVWlTbGlkZXIuc2V0KCBbIG51bGwsIG1heFZhbHVlIF0gKTtcblxuXHRcdFx0XHRcdFx0ZmlsdGVyUHJvZHVjdHNBY2NvcmRpbmdUb1NsaWRlciggc2xpZGVyLm5vVWlTbGlkZXIuZ2V0KCkgKTtcblx0XHRcdFx0XHR9LCBkZWxheSApICk7XG5cdFx0XHRcdH0gKTtcblx0XHRcdH0gKTtcblx0XHR9LFxuXHRcdGluaXREYXRlcGlja2VyOiBmdW5jdGlvbigpIHtcblx0XHRcdGlmICggISBqUXVlcnkoKS5kYXRlcGlja2VyICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGNvbnN0ICR3Y2FwZkRhdGVGaWx0ZXIgPSAkYm9keS5maW5kKCAnLndjYXBmLWRhdGUtaW5wdXQnICk7XG5cblx0XHRcdGNvbnN0IGZvcm1hdCAgICAgICAgPSAkd2NhcGZEYXRlRmlsdGVyLmF0dHIoICdkYXRhLWRhdGUtZm9ybWF0JyApO1xuXHRcdFx0Y29uc3QgeWVhckRyb3Bkb3duICA9ICR3Y2FwZkRhdGVGaWx0ZXIuYXR0ciggJ2RhdGEtZGF0ZS1waWNrZXIteWVhci1kcm9wZG93bicgKTtcblx0XHRcdGNvbnN0IG1vbnRoRHJvcGRvd24gPSAkd2NhcGZEYXRlRmlsdGVyLmF0dHIoICdkYXRhLWRhdGUtcGlja2VyLW1vbnRoLWRyb3Bkb3duJyApO1xuXG5cdFx0XHRjb25zdCAkZnJvbSA9ICR3Y2FwZkRhdGVGaWx0ZXIuZmluZCggJy5kYXRlLWZyb20taW5wdXQnICk7XG5cdFx0XHRjb25zdCAkdG8gICA9ICR3Y2FwZkRhdGVGaWx0ZXIuZmluZCggJy5kYXRlLXRvLWlucHV0JyApO1xuXG5cdFx0XHQkZnJvbS5kYXRlcGlja2VyKCB7XG5cdFx0XHRcdGRhdGVGb3JtYXQ6IGZvcm1hdCxcblx0XHRcdFx0Y2hhbmdlWWVhcjogeWVhckRyb3Bkb3duLFxuXHRcdFx0XHRjaGFuZ2VNb250aDogbW9udGhEcm9wZG93bixcblx0XHRcdH0gKTtcblxuXHRcdFx0JHRvLmRhdGVwaWNrZXIoIHtcblx0XHRcdFx0ZGF0ZUZvcm1hdDogZm9ybWF0LFxuXHRcdFx0XHRjaGFuZ2VZZWFyOiB5ZWFyRHJvcGRvd24sXG5cdFx0XHRcdGNoYW5nZU1vbnRoOiBtb250aERyb3Bkb3duLFxuXHRcdFx0fSApO1xuXHRcdH0sXG5cdFx0aW5pdEZpbHRlck9wdGlvblRvb2x0aXA6IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gbm9pbnNwZWN0aW9uIEpTVW5yZXNvbHZlZFJlZmVyZW5jZVxuXHRcdFx0aWYgKCAnZnVuY3Rpb24nICE9PSB0eXBlb2YgdGlwcHkgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0aWYgKCAhIHdjYXBmX3BhcmFtcy51c2VfdGlwcHlqcyApIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXG5cdFx0XHRjb25zdCB0b29sdGlwUG9zaXRpb25zID0gWyAndG9wJywgJ3JpZ2h0JywgJ2JvdHRvbScsICdsZWZ0JyBdO1xuXG5cdFx0XHR0b29sdGlwUG9zaXRpb25zLmZvckVhY2goIGZ1bmN0aW9uKCB0b29sdGlwUG9zaXRpb24gKSB7XG5cdFx0XHRcdGNvbnN0IGlkZW50aWZpZXIgPSAnZGF0YS13Y2FwZi10b29sdGlwLScgKyB0b29sdGlwUG9zaXRpb247XG5cblx0XHRcdFx0Ly8gbm9pbnNwZWN0aW9uIEpTVW5yZXNvbHZlZFJlZmVyZW5jZVxuXHRcdFx0XHRjb25zdCBpbnN0YW5jZXMgPSB0aXBweSggJ1snICsgaWRlbnRpZmllciArICddJywge1xuXHRcdFx0XHRcdHBsYWNlbWVudDogdG9vbHRpcFBvc2l0aW9uLFxuXHRcdFx0XHRcdGNvbnRlbnQoIHJlZmVyZW5jZSApIHtcblx0XHRcdFx0XHRcdHJldHVybiByZWZlcmVuY2UuZ2V0QXR0cmlidXRlKCBpZGVudGlmaWVyICk7XG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRhbGxvd0hUTUw6IHRydWUsXG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHR3aW5kb3cudGlwcHlJbnN0YW5jZXMgPSB0aXBweUluc3RhbmNlcy5jb25jYXQoIGluc3RhbmNlcyApO1xuXHRcdFx0fSApO1xuXHRcdH0sXG5cdFx0aW5pdDogZnVuY3Rpb24oKSB7XG5cdFx0XHRXQ0FQRi5pbml0Q29tYm9ib3goKTtcblx0XHRcdFdDQVBGLmluaXRSYW5nZVNsaWRlcigpO1xuXHRcdFx0V0NBUEYuaW5pdERhdGVwaWNrZXIoKTtcblx0XHRcdFdDQVBGLmluaXRGaWx0ZXJPcHRpb25Ub29sdGlwKCk7XG5cdFx0fSxcblx0XHRpbml0UG9wU3RhdGU6IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCB3Y2FwZl9wYXJhbXMucmVsb2FkX29uX2JhY2sgJiYgd2NhcGZfcGFyYW1zLmZvdW5kX3djYXBmICkge1xuXHRcdFx0XHRoaXN0b3J5LnJlcGxhY2VTdGF0ZSggeyB3Y2FwZjogdHJ1ZSB9LCAnJywgd2luZG93LmxvY2F0aW9uICk7XG5cblx0XHRcdFx0Ly8gSGFuZGxlIHRoZSBwb3BzdGF0ZSBldmVudChicm93c2VyJ3MgYmFjay9mb3J3YXJkKVxuXHRcdFx0XHR3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciggJ3BvcHN0YXRlJywgZnVuY3Rpb24oIGUgKSB7XG5cdFx0XHRcdFx0aWYgKCBudWxsICE9PSBlLnN0YXRlICYmIGUuc3RhdGUuaGFzT3duUHJvcGVydHkoICd3Y2FwZicgKSApIHtcblx0XHRcdFx0XHRcdFdDQVBGLmZpbHRlclByb2R1Y3RzKCAncG9wc3RhdGUnICk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9ICk7XG5cdFx0XHR9XG5cdFx0fVxuXHR9O1xuXG5cdC8qKlxuXHQgKiBFbmFibGUgaXQgaWYgbmVjZXNzYXJ5LlxuXHQgKlxuXHQgKiBAc291cmNlIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8zMzAwNDkxN1xuXHQgKi9cblx0aWYgKCAnc2Nyb2xsUmVzdG9yYXRpb24nIGluIGhpc3RvcnkgKSB7XG5cdFx0Ly8gaGlzdG9yeS5zY3JvbGxSZXN0b3JhdGlvbiA9ICdtYW51YWwnO1xuXHR9XG5cbn0oIGpRdWVyeSwgd2luZG93ICkgKTtcblxuKCBmdW5jdGlvbiggJCwgV0NBUEYgKSB7XG5cblx0V0NBUEYuaW5pdCgpO1xuXHRXQ0FQRi5pbml0UG9wU3RhdGUoKTtcblxuXHRXQ0FQRi5oYW5kbGVGaWx0ZXJBY2NvcmRpb24oKTtcblx0V0NBUEYuaGFuZGxlSGllcmFyY2h5VG9nZ2xlKCk7XG5cdFdDQVBGLmhhbmRsZVNvZnRMaW1pdCgpO1xuXHRXQ0FQRi5oYW5kbGVTZWFyY2hGaWx0ZXJPcHRpb25zKCk7XG5cblx0V0NBUEYuaGFuZGxlTGlzdEZpbHRlcnMoKTtcblx0V0NBUEYuaGFuZGxlRHJvcGRvd25GaWx0ZXJzKCk7XG5cdFdDQVBGLmhhbmRsZU51bWJlcklucHV0RmlsdGVycygpO1xuXHRXQ0FQRi5oYW5kbGVEYXRlSW5wdXRGaWx0ZXJzKCk7XG5cdFdDQVBGLmhhbmRsZVBhZ2luYXRpb24oKTtcblx0V0NBUEYuaGFuZGxlRGVmYXVsdE9yZGVyYnkoKTtcblxuXHRXQ0FQRi5oYW5kbGVDbGVhckZpbHRlcigpO1xuXG5cdFdDQVBGLmhhbmRsZUZpbHRlclRvb2x0aXAoKTtcblxuXHQvKipcblx0ICogTWFrZSBpdCBjb21wYXRpYmxlIHdpdGggb3RoZXIgcGx1Z2lucy5cblx0ICovXG5cdCQoIGRvY3VtZW50ICkub24oICd3Y2FwZl9hZnRlcl91cGRhdGluZ19wcm9kdWN0cycsIGZ1bmN0aW9uKCkge1xuXHRcdC8vIHdvby12YXJpYXRpb24tc3dhdGNoZXNcblx0XHQkKCBkb2N1bWVudCApLnRyaWdnZXIoICd3b29fdmFyaWF0aW9uX3N3YXRjaGVzX3Byb19pbml0JyApO1xuXHR9ICk7XG5cbn0oIGpRdWVyeSwgd2luZG93LldDQVBGICkgKTtcbiIsIi8qKlxuICogQHNvdXJjZSBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzQxNDE4MTNcbiAqXG4gKiBAcGFyYW0gbnVtYmVyXG4gKiBAcGFyYW0gZGVjaW1hbHNcbiAqIEBwYXJhbSBkZWNfcG9pbnRcbiAqIEBwYXJhbSB0aG91c2FuZHNfc2VwXG4gKlxuICogQHJldHVybnMge3N0cmluZ31cbiAqL1xuZnVuY3Rpb24gbnVtYmVyRm9ybWF0KCBudW1iZXIsIGRlY2ltYWxzLCBkZWNfcG9pbnQsIHRob3VzYW5kc19zZXAgKSB7XG5cdC8vIFN0cmlwIGFsbCBjaGFyYWN0ZXJzIGJ1dCBudW1lcmljYWwgb25lcy5cblx0bnVtYmVyID0gKCBudW1iZXIgKyAnJyApLnJlcGxhY2UoIC9bXlxcZCtcXC1FZS5dL2csICcnICk7XG5cblx0Y29uc3QgbiAgICA9ICEgaXNGaW5pdGUoICtudW1iZXIgKSA/IDAgOiArbnVtYmVyO1xuXHRjb25zdCBwcmVjID0gISBpc0Zpbml0ZSggK2RlY2ltYWxzICkgPyAwIDogTWF0aC5hYnMoIGRlY2ltYWxzICk7XG5cdGNvbnN0IHNlcCAgPSAoIHR5cGVvZiB0aG91c2FuZHNfc2VwID09PSAndW5kZWZpbmVkJyApID8gJywnIDogdGhvdXNhbmRzX3NlcDtcblx0Y29uc3QgZGVjICA9ICggdHlwZW9mIGRlY19wb2ludCA9PT0gJ3VuZGVmaW5lZCcgKSA/ICcuJyA6IGRlY19wb2ludDtcblxuXHRsZXQgcztcblxuXHRjb25zdCB0b0ZpeGVkRml4ID0gZnVuY3Rpb24oIG4sIHByZWMgKSB7XG5cdFx0Y29uc3QgayA9IE1hdGgucG93KCAxMCwgcHJlYyApO1xuXHRcdHJldHVybiAnJyArIE1hdGgucm91bmQoIG4gKiBrICkgLyBrO1xuXHR9O1xuXG5cdC8vIEZpeCBmb3IgSUUgcGFyc2VGbG9hdCgwLjU1KS50b0ZpeGVkKDApID0gMDtcblx0cyA9ICggcHJlYyA/IHRvRml4ZWRGaXgoIG4sIHByZWMgKSA6ICcnICsgTWF0aC5yb3VuZCggbiApICkuc3BsaXQoICcuJyApO1xuXG5cdGlmICggc1sgMCBdLmxlbmd0aCA+IDMgKSB7XG5cdFx0c1sgMCBdID0gc1sgMCBdLnJlcGxhY2UoIC9cXEIoPz0oPzpcXGR7M30pKyg/IVxcZCkpL2csIHNlcCApO1xuXHR9XG5cblx0aWYgKCAoIHNbIDEgXSB8fCAnJyApLmxlbmd0aCA8IHByZWMgKSB7XG5cdFx0c1sgMSBdID0gc1sgMSBdIHx8ICcnO1xuXHRcdHNbIDEgXSArPSBuZXcgQXJyYXkoIHByZWMgLSBzWyAxIF0ubGVuZ3RoICsgMSApLmpvaW4oICcwJyApO1xuXHR9XG5cblx0cmV0dXJuIHMuam9pbiggZGVjICk7XG59XG5cbmZ1bmN0aW9uIGNsZWFuVXJsKCB1cmwgKSB7XG5cdHJldHVybiB1cmwucmVwbGFjZSggLyUyQy9nLCAnLCcgKTtcbn1cblxuZnVuY3Rpb24gZ2V0T3JkZXJCeVVybCggdXJsICkge1xuXHRjb25zdCBwYWdlZCA9IHBhcnNlSW50KCB1cmwucmVwbGFjZSggLy4rXFwvcGFnZVxcLyhcXGQrKSsvLCAnJDEnICkgKTtcblxuXHRpZiAoIHBhZ2VkICkge1xuXHRcdHVybCA9IHVybC5yZXBsYWNlKCAvcGFnZVxcLyhcXGQrKVxcLy8sICcnICk7XG5cdH1cblxuXHRyZXR1cm4gY2xlYW5VcmwoIHVybCApO1xufVxuIl19
