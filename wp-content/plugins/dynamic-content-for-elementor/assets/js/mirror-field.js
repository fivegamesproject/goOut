"use strict";

(() => {
	let getFieldValue = (form, id) => {
		let data = new FormData(form);
		let key = `form_fields[${id}]`;
		if (data.has(key)) {
			return data.get(key);
		}
		key = `form_fields[${id}][]`
		if (data.has(key))  {
			return data.getAll(key).join(', ');
		}
		return "";
	}

	function initializeMirrorField(wrapper, widget) {
		let input = wrapper.getElementsByTagName('input')[0];
		let form = widget.getElementsByTagName('form')[0];
		let sourceFieldId = input.dataset.sourceFieldId;
		let realTime = input.dataset.realTime === 'yes';
		if (input.dataset.hide == 'yes') {
			wrapper.style.display = "none";
		}
		let prevInputValue = '';
		const onChange = () => {
			let newValue = getFieldValue(form, sourceFieldId);
			if (input.value !== prevInputValue) {
				// mirror changed directly, stop mirroring.
				form.removeEventListener(realTime ? 'input' : 'change', onChange);
				return;
			}
			if (input.value === newValue) {
				// source not changed, nothing to do.
				return;
			}
			input.value = newValue;
			prevInputValue = newValue;
			if ("createEvent" in document) {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent("change", false, true);
				input.dispatchEvent(evt);
			}
			else {
				input.fireEvent("onchange");
			}
		}
		onChange();
		form.addEventListener(realTime ? 'input' : 'change', onChange);
	}

	function initializeAllMirrorFields($scope) {
		$scope.find('.elementor-field-type-dce_mirror_field').each((_, w) => initializeMirrorField(w, $scope[0]));
	}

	jQuery(window).on('elementor/frontend/init', function() {
		if(elementorFrontend.isEditMode()) {
			return;
		}
		elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllMirrorFields);
	});
})();
