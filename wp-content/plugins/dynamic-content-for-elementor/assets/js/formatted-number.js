"use strict";

(() => {
	function initializeFormattedNumberField(wrapper, widget) {
		let realInput = wrapper.getElementsByClassName('dce-format-real-input')[0];
		let interactiveInput = wrapper.getElementsByClassName('dce-format-interactive-input')[0];
		let form = widget.getElementsByTagName('form')[0];
		let realTime = realInput.dataset.realTime === 'yes';
		let prevInputValue = '';
		let locales = undefined;
		let style = realInput.dataset.style;
		let locale = realInput.dataset.locale;
		if (locale === 'auto') {
			locale = undefined;
		}
		let formatOptions = {style: style};
		if (style === 'currency') {
			formatOptions.currency = realInput.dataset.currency;
		}

		const onFocus = () => {
			interactiveInput.removeEventListener('focus', onFocus);
			interactiveInput.value = realInput.value;
			interactiveInput.type = 'number';
			interactiveInput.style.cursor = 'auto';
		}
		const onBlur = () => {
			realInput.value = interactiveInput.value;
			interactiveInput.type = 'text';
			try {
				interactiveInput.value = Number(realInput.value).toLocaleString(locale, formatOptions);
			} catch (error) {
				interactiveInput.value = error.message;
			}
			interactiveInput.style.cursor = 'pointer';
			interactiveInput.addEventListener('focus', onFocus);
			if ("createEvent" in document) {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent("change", false, true);
				realInput.dispatchEvent(evt);
			}
			else {
				realInput.fireEvent("onchange");
			}
		}
		onBlur();
		interactiveInput.addEventListener('blur', onBlur);
	}

	function initializeAllFormattedNumberFields($scope) {
		$scope.find('.elementor-field-type-dce_formatted_number').each((_, w) => initializeFormattedNumberField(w, $scope[0]));
	}

	jQuery(window).on('elementor/frontend/init', function() {
		if(elementorFrontend.isEditMode()) {
			return;
		}
		elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllFormattedNumberFields);
	});
})();
