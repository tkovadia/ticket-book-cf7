jQuery(document).ready(function() {
	jQuery.fancybox("#hidden");

    //Register click events to all checkboxes inside question element
    jQuery(document).on('click', 'fieldset input:checkbox', function() {
        alert(1);
		//Find the next answer element to the question and based on the checked status call either show or hide method
        jQuery(this).parent(fieldset).next(select)[this.checked? 'show' : 'hide']()
    });

});
