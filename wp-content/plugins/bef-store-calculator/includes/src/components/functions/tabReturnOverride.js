export function tabReturnOverride(jQuery) {
  // register jQuery extension
  // capture all selectable elements in the form/page
  jQuery.extend(jQuery.expr[':'], {
    focusable(el, index, selector) {
      // ignore hidden fields as selectable
      if (!el.classList.contains('gform_hidden')) {
        return jQuery(el).not('disabled').not('readonly').is('input, textarea, [tabindex]');
      }
    },
  });

  // recursive search to find next active selection to tab to.
  function findNextActive(func, index) {
    // If input is disabled, skip it and try the next one.
    if (!func.eq(index)[0].disabled) {
      func.eq(index).focus();
      return;
    }
    findNextActive(func, index + 1);
  }

  jQuery(document).on('keydown', 'input', function (e) {
    const code = e.keyCode || e.which;
    if (code === 13 && !jQuery(e.target).is('textarea,input[type="submit"],input[type="button"]')) {
      e.preventDefault();
      // Get all focusable elements on the page
      const canFocus = jQuery(':focusable');
      const index = canFocus.index(this) + 1;
      // Simple error checking
      if (index <= canFocus.length) {
        findNextActive(canFocus, index);
      }
    }
  });
}
