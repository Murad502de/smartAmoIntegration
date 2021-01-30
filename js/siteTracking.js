/*
	* Unten wird amo_pixel_identifier_js hinzugefügt und AMOPIXEL_IDENTIFIER im callback erstellt, nachdem amo_pixel_identifier_js vollständig geladen wurde (de)
	* Below is adding amo_pixel_identifier_js and creating AMOPIXEL_IDENTIFIER in callback after amo_pixel_identifier_js is fully loaded (en)
	* Ниже добавление amo_pixel_identifier_js и создание AMOPIXEL_IDENTIFIER в callback после полной загрузки amo_pixel_identifier_js (ru)
*/

function loadScript(src, callback)
{
	let script = document.createElement('script');
	script.src = src;
	script.id = "amo_pixel_identifier_js";
	script.async = "async";

	window.onload = () => callback();

	document.head.append(script);
}

/*
	* loadScript Funktionsaufruf (de)
	* Calling the loadScript function (en)
	* Вызов функции loadScript (ru)
*/

loadScript("https://piper.amocrm.ru/pixel/js/identifier/pixel_identifier.js", function(){
	setTimeout(() => {
		let id = window.AMOPIXEL_IDENTIFIER.getVisitorUid();

		console.log('hello ' + id); // Debugging | Debug | Отладка

		/*
			* Unten muss AMOPIXEL_IDENTIFIER bearbeitet werden (de)
			* AMOPIXEL_IDENTIFIER must be edited below (en)
			* Ниже AMOPIXEL_IDENTIFIER должен быть обработан (ru)
		*/

		let amoField = document.querySelector('#amoId');
		amoField.value = id;
		console.log(amoField); // Debugging | Debug | Отладка
    }, 500);
    
    /*
	    * unten muss amoPixel gestanden werden (de)
	    * amoPixel should be at the bottom (en)
	    * amoPixel должен находиться внизу (ru)
    */

    (function (w, d) {w.amo_pixel_token = 'p1Ip4iB8yqaEyXU9HPmcTA5oQYpKvIcrjLSQ7SHQL1ERgnJKZFbWcMk8qMiuPsoH';var s = document.createElement('script'), f = d.getElementsByTagName('script')[0];s.id = 'amo_pixel_js';s.type = 'text/javascript';s.async = true;s.src = 'https://piper.amocrm.ru/pixel/js/tracker/pixel.js?token=' + w.amo_pixel_token;f.parentNode.insertBefore(s, f);})(window, document);
});