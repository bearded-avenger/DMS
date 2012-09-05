/* =========================================================
 * ModeLess
 * =========================================================
 */

!function ($) {

  "use strict"; // jshint


 /* MODAL CLASS DEFINITION
  * ====================== */

  var ModeLess = function (element, options) {
    this.options = options
    this.$element = $(element)
		.addClass('fade hide')
    	.delegate('[data-dismiss="modeless"]', 'click.dismiss.modeless', $.proxy(this.hide, this))
    this.options.remote && this.$element.find('.modeless-body').load(this.options.remote)
  }

  ModeLess.prototype = {

      constructor: ModeLess

    , toggle: function () {

        return this[!this.isShown ? 'show' : 'hide']()
      }

    , show: function () {
        var that = this
          , e = $.Event('show')

        this.$element.trigger(e)

        if (this.isShown || e.isDefaultPrevented()) return

        $('body').addClass('modeless-open')

        this.isShown = true

        this.escape()

          var transition = $.support.transition && that.$element.hasClass('fade')

          if (!that.$element.parent().length) {
            that.$element.appendTo(document.body) //don't move modals dom position
          }

          that.$element
            .show()

          if (transition) {
            that.$element[0].offsetWidth // force reflow
          }

          that.$element
            .addClass('in')
            .attr('aria-hidden', false)
            .focus()

          that.enforceFocus()

          transition ?
            that.$element.one($.support.transition.end, function () { that.$element.trigger('shown') }) :
            that.$element.trigger('shown')

      }

    , hide: function (e) {
		
        e && e.preventDefault()

        var that = this

        e = $.Event('hide')

        this.$element.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        $('body').removeClass('modal-open')

        this.escape()

        $(document).off('focusin.modal')

        this.$element
          .removeClass('in')
          .attr('aria-hidden', true)

        $.support.transition && this.$element.hasClass('fade') ?
          this.hideWithTransition() :
          this.hideModal()
      }

    , enforceFocus: function () {
        var that = this
        $(document).on('focusin.modeless', function (e) {
          if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
            that.$element.focus()
          }
        })
      }

    , escape: function () {
        var that = this
        if (this.isShown && this.options.keyboard) {
          this.$element.on('keyup.dismiss.modeless', function ( e ) {
            e.which == 27 && that.hide()
          })
        } else if (!this.isShown) {
          this.$element.off('keyup.dismiss.modeless')
        }
      }

    , hideWithTransition: function () {
        var that = this
          , timeout = setTimeout(function () {
              that.$element.off($.support.transition.end)
              that.hideModeless()
            }, 500)

        this.$element.one($.support.transition.end, function () {
          clearTimeout(timeout)
          that.hideModeless()
        })
      }

    , hideModeless: function (that) {
        this.$element
          .hide()
          .trigger('hidden')

      }

   
  }


 /* MODAL PLUGIN DEFINITION
  * ======================= */

  $.fn.modeless = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('modeless')
        , options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option)
      if (!data) $this.data('modeless', (data = new ModeLess(this, options)))
      if (typeof option == 'string') data[option]()
      else if (options.show) data.show()
    })
  }

  $.fn.modeless.defaults = {
      backdrop: true
    , keyboard: true
    , show: true
  }

  $.fn.modeless.Constructor = ModeLess


 /* MODAL DATA-API
  * ============== */

  $(function () {
    $('body').on('click.modeless.data-api', '[data-toggle="modeless"]', function ( e ) {
      var $this = $(this)
        , href = $this.attr('href')
        , $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
        , option = $target.data('modeless') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

      e.preventDefault()

      $target
        .modeless(option)
        .one('hide', function () {
          $this.focus()
        })
    })
  })

}(window.jQuery);