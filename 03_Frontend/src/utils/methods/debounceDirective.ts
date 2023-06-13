import debounce from './debounce';

directive.debounce = debounce

function directive (el: any, binding: any) {
  if (binding.value !== binding.oldValue) { // change debounce only if interval has changed
    el.oninput = directive.debounce(function (event: any) {
      el.dispatchEvent(new Event('change'))
    }, parseInt(binding.value) || 500)
  }
}

export default directive;