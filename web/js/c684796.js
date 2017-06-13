$(function() {
	/**
	 * COMMON FUNCTIONS
	 * ----------------
	 */

	 function convertDatePhpToFrench(date) {
        var parts = date.split('-');

        return parts[2]+'/'+parts[1]+'/'+parts[0];
    }

	/**
	 * ----------------
	 */
	


	/**
	 * TRANSLATIONS
	 * ------------
	 */
	
	function addTranslations() {
		Translator.add(
		    'ots_billing.step3.full',
		    'Full-day',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.step3.full',
		    'Journée complète',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.step3.half',
		    'Half-day',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.step3.half',
		    'Demi-journée',
		    'messages',
		    'fr'
		);
	}

	/**
	 * ------------
	 */






	/**
     * FILL RECAP VALUES FOR STEP 3
     * ----------------------------
     */

    function fillRecap() {
        var date = convertDatePhpToFrench( $('#ots_billingbundle_ticketorder_date').val() );
        $('#recap_date').text(date);
        
        var type = $('#ots_billingbundle_ticketorder_type').val() === '1' ? Translator.trans('ots_billing.step3.full') : Translator.trans('ots_billing.step3.half');
        $('#recap_type').text(type);
        
        $('#recap_nbTickets').text( $('#ots_billingbundle_ticketorder_nbTickets').val() );
        
        $('#recap_price').text( $('#ots_billingbundle_ticketorder_price').val()+'€' );
     }







     addTranslations();

    fillRecap();
});
$(function() {
	function addTranslations() {
		Translator.add(
		    'ots_billing.stripe.name',
		    'Louvre Museum',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.name',
		    'Musée du Louvre',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.stripe.description',
		    '{1}%count% ticket|[2,+Inf]%count% tickets',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.description',
		    '{1}%count% billet|[2,+Inf]%count% billets',
		    'messages',
		    'fr'
		);

		Translator.add(
		    'ots_billing.stripe.button',
		    'Pay {{amount}}',
		    'messages',
		    'en'
		);
		Translator.add(
		    'ots_billing.stripe.button',
		    'Payer {{amount}}',
		    'messages',
		    'fr'
		);
	}

	function setupStripeCheckout() {
		var btn = $('#btn-stripe-checkout');
	
		addTranslations();

		var handler = StripeCheckout.configure({
		  	key:    'pk_test_hoXdqVAmVW3pIQOWqanoshlw',
		  	image:  'https://stripe.com/img/documentation/checkout/marketplace.png',
		  	locale: 'auto',
		  	allowRememberMe: false,
		  	panelLabel: Translator.trans('ots_billing.stripe.button'),
		  	token:  function(token) {
		    	//we fill the checkout token hidden field of the form with the token id so it's passed to back-end too
		    	$('#ots_billingbundle_ticketorder_checkoutToken').val(token.id);

		    	//then we send the form
		    	$('form[name="ots_billingbundle_ticketorder"]').submit();
		  	}
		});
	
		btn.on('click', function(e) {
			e.preventDefault();
			
			var nbTickets = btn.data('nbtickets');

			// Open Checkout with further options:
			handler.open({
				name:        Translator.trans('ots_billing.stripe.name'),
			    description: Translator.transChoice('ots_billing.stripe.description', nbTickets, {"count" : nbTickets}),
			    currency:    'eur',
			    amount:      btn.data('amount')
			});
		});
	
		// Close Checkout on page navigation:
		$(window).on('popstate', function() {
			handler.close();
		});
	}

	setupStripeCheckout();
});
/**
 * @author William DURAND <william.durand1@gmail.com>
 * @license MIT Licensed
 */
!function(e,a){"function"==typeof define&&define.amd?define("Translator",a):"object"==typeof module&&module.exports?module.exports=a():e.Translator=a()}(this,function(){"use strict";function e(e,a){var s,n=p.placeHolderPrefix,t=p.placeHolderSuffix;for(s in a){var c=new RegExp(n+s+t,"g");c.test(e)&&(e=e.replace(c,a[s]))}return e}function a(e,a,n,t,c){var r=n||t||c,i=a,u=r.split("_")[0];if(!(r in l))if(u in l)r=u;else{if(!(c in l))return e;r=c}if("undefined"==typeof i||null===i)for(var o=0;o<f.length;o++)if(s(r,f[o],e)||s(u,f[o],e)||s(c,f[o],e)){i=f[o];break}if(s(r,i,e))return l[r][i][e];for(var d,h,p,m;r.length>2&&(d=r.length,h=r.split(/[\s_]+/),p=h[h.length-1],m=p.length,1!==h.length);)if(r=r.substring(0,d-(m+1)),s(r,i,e))return l[r][i][e];return s(c,i,e)?l[c][i][e]:e}function s(e,a,s){return e in l&&(a in l[e]&&s in l[e][a])}function n(e,a,s){var n,r,i=[],l=[],u=e.split(p.pluralSeparator),f=[];for(n=0;n<u.length;n++){var m=u[n];d.test(m)?(f=m.match(d),i[f[0]]=f[f.length-1]):o.test(m)?(f=m.match(o),l.push(f[1])):l.push(m)}for(r in i)if(h.test(r))if(f=r.match(h),f[1]){var g,v=f[2].split(",");for(g in v)if(a==v[g])return i[r]}else{var b=t(f[4]),k=t(f[5]);if(("["===f[3]?a>=b:a>b)&&("]"===f[6]?a<=k:a<k))return i[r]}return l[c(a,s)]||l[0]||void 0}function t(e){return"-Inf"===e?Number.NEGATIVE_INFINITY:"+Inf"===e||"Inf"===e?Number.POSITIVE_INFINITY:parseInt(e,10)}function c(e,a){var s=a;switch("pt_BR"===s&&(s="xbr"),s.length>3&&(s=s.split("_")[0]),s){case"bo":case"dz":case"id":case"ja":case"jv":case"ka":case"km":case"kn":case"ko":case"ms":case"th":case"tr":case"vi":case"zh":return 0;case"af":case"az":case"bn":case"bg":case"ca":case"da":case"de":case"el":case"en":case"eo":case"es":case"et":case"eu":case"fa":case"fi":case"fo":case"fur":case"fy":case"gl":case"gu":case"ha":case"he":case"hu":case"is":case"it":case"ku":case"lb":case"ml":case"mn":case"mr":case"nah":case"nb":case"ne":case"nl":case"nn":case"no":case"om":case"or":case"pa":case"pap":case"ps":case"pt":case"so":case"sq":case"sv":case"sw":case"ta":case"te":case"tk":case"ur":case"zu":return 1==e?0:1;case"am":case"bh":case"fil":case"fr":case"gun":case"hi":case"ln":case"mg":case"nso":case"xbr":case"ti":case"wa":return 0===e||1==e?0:1;case"be":case"bs":case"hr":case"ru":case"sr":case"uk":return e%10==1&&e%100!=11?0:e%10>=2&&e%10<=4&&(e%100<10||e%100>=20)?1:2;case"cs":case"sk":return 1==e?0:e>=2&&e<=4?1:2;case"ga":return 1==e?0:2==e?1:2;case"lt":return e%10==1&&e%100!=11?0:e%10>=2&&(e%100<10||e%100>=20)?1:2;case"sl":return e%100==1?0:e%100==2?1:e%100==3||e%100==4?2:3;case"mk":return e%10==1?0:1;case"mt":return 1==e?0:0===e||e%100>1&&e%100<11?1:e%100>10&&e%100<20?2:3;case"lv":return 0===e?0:e%10==1&&e%100!=11?1:2;case"pl":return 1==e?0:e%10>=2&&e%10<=4&&(e%100<12||e%100>14)?1:2;case"cy":return 1==e?0:2==e?1:8==e||11==e?2:3;case"ro":return 1==e?0:0===e||e%100>0&&e%100<20?1:2;case"ar":return 0===e?0:1==e?1:2==e?2:e>=3&&e<=10?3:e>=11&&e<=99?4:5;default:return 0}}function r(e,a){for(var s=0;s<e.length;s++)if(a===e[s])return!0;return!1}function i(){return"undefined"!=typeof document?document.documentElement.lang.replace("-","_"):u}var l={},u="en",f=[],o=new RegExp(/^\w+\: +(.+)$/),d=new RegExp(/^\s*((\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]]))\s?(.+?)$/),h=new RegExp(/^\s*(\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/),p={locale:i(),fallback:u,placeHolderPrefix:"%",placeHolderSuffix:"%",defaultDomain:"messages",pluralSeparator:"|",add:function(e,a,s,n){var t=n||this.locale||this.fallback,c=s||this.defaultDomain;return l[t]||(l[t]={}),l[t][c]||(l[t][c]={}),l[t][c][e]=a,!1===r(f,c)&&f.push(c),this},trans:function(s,n,t,c){var r=a(s,t,c,this.locale,this.fallback);return e(r,n||{})},transChoice:function(s,t,c,r,i){var l=a(s,r,i,this.locale,this.fallback),u=parseInt(t,10);return c=c||{},void 0===c.count&&(c.count=t),"undefined"==typeof l||isNaN(u)||(l=n(l,u,i||this.locale||this.fallback)),e(l,c)},fromJSON:function(e){if("string"==typeof e&&(e=JSON.parse(e)),e.locale&&(this.locale=e.locale),e.fallback&&(this.fallback=e.fallback),e.defaultDomain&&(this.defaultDomain=e.defaultDomain),e.translations)for(var a in e.translations)for(var s in e.translations[a])for(var n in e.translations[a][s])this.add(n,e.translations[a][s][n],s,a);return this},reset:function(){l={},f=[],this.locale=i()}};return p});
