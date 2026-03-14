(function ( $, x5engine ) {
	var x = x5engine,
		s = x.settings,
		p = s.currentPath,
		b = x.boot;

	s.siteId = 'A09D4C6CECDB84B21C39282B7B300242';
	s.dateFormat = 'dd MMM yyyy';
	s.dateFormatExt = 'dddd dd MMM yyyy';
	s.rtl = false;
	s.version = '2025-3-6-0';
	b.push(function () {
		x.setupDateTime();
		x.imAccess.showLogout();
		x.utils.autoHeight();
		x.cart.ui.updateWidget();
		x.imGrid.init();
	});
	b.push(function () {
		x.stickyBar({
			enabledBreakpoints: ['ea2f0ee4d5cbb25e1ee6c7c4378fee7b','d2f9bff7f63c0d6b7c7d55510409c19b','72e5146e7d399bc2f8a12127e43469f1','5ecdcca63de80fd3d4fbb36295d22b7d']
		});
	});

	// Links
	$.extend(s.links, {
		'htmlPlaceholder' : '<!--html_content_placeholder-->',
		'cssPlaceholder' : '<!--css_class_placeholder-->',
		'baseUrlPlaceholder' : '--base_url_placeholder--'
	});


	// ShowBox
	$.extend(s.imShowBox, {
		"effect": "none", "customEffect": "generic animated none",
		'transitionEffect' : 'fade',
		'fullScreenEnabled' : true,
		'zoomEnabled' : true,
		'showProgress' : false,
		'shadow' : '',
		'background' : 'rgba(42, 54, 59, 0.9)',
		'backgroundBlur' : false,
		'borderWidth' : {
			'top': 0,
			'right': 0,
			'bottom': 0,
			'left': 0
		},
		'buttonLeft': '<svg class=\"im-common-left-button\"  xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}<\/style><\/defs><path class=\"a\" d=\"M15.53,24,34.74,1.25V3.89l-16.91,20a1.54,1.54,0,0,0,0,2L34.74,46V48.6L15.53,25.83a1.66,1.66,0,0,1,0-1.83Z\"/><path class=\"b\" d=\"M34.74,43.47,19.17,25,34.74,6.52V3.88l-17,20.21a1.66,1.66,0,0,0,0,1.83l17,20.19Z\"/><\/svg>',
		'buttonRight': '<svg class=\"im-common-right-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}<\/style><\/defs><path class=\"a\" d=\"M34.43,24.08,15.29,1.42V4.05L32.14,24a1.54,1.54,0,0,1,0,2l-16.85,20v2.63L34.43,25.9a1.65,1.65,0,0,0,0-1.83Z\"/><path class=\"b\" d=\"M15.29,43.47,30.81,25.08,15.29,6.67V4l17,20.13a1.65,1.65,0,0,1,0,1.83l-17,20.11Z\"/><\/svg>',
		'buttonClose': '<svg class=\"im-common-close-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#f6f6f6;}.b{fill:#3e3d40;}<\/style><\/defs><polygon class=\"a\" points=\"25.28 25.11 25.28 25.11 10.24 11.23 10.24 13.31 22.71 24.82 22.71 24.82 39.76 40.72 39.76 38.64 25.28 25.11\"/><polygon class=\"b\" points=\"27.48 25.18 27.49 25.18 10.24 9.29 10.24 11.36 24.91 24.89 24.91 24.89 39.76 38.77 39.76 36.69 27.48 25.18\"/><polygon class=\"a\" points=\"24.91 25.11 24.91 25.11 39.76 11.23 39.76 13.31 27.48 24.82 27.49 24.82 10.24 40.72 10.24 38.64 24.91 25.11\"/><polygon class=\"b\" points=\"22.71 25.18 22.71 25.18 39.76 9.29 39.76 11.36 25.28 24.89 25.28 24.89 10.24 38.77 10.24 36.69 22.71 25.18\"/><\/svg>',
		'buttonEnterFS': '<svg class=\"im-common-enter-fs-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}<\/style><\/defs><path class=\"a\" d=\"M7.59,41.46l.06-11a.55.55,0,0,0-.13-.36L5.89,28.19v1.32c0,.24,0,1,0,2.08-.1,6.9-.15,10.6-.16,11.77a.54.54,0,0,0,.53.55H22L20,42.26a.55.55,0,0,0-.35-.13L8.13,42A.55.55,0,0,1,7.59,41.46Z\"/><path class=\"b\" d=\"M9.34,39.86l.05-7.62a.54.54,0,0,0-.15-.38l-1.6-1.64L7.59,41.46a.54.54,0,0,0,.54.55l11.76.13-1.74-1.49a.54.54,0,0,0-.35-.13l-7.91-.11A.54.54,0,0,1,9.34,39.86Z\"/><path class=\"a\" d=\"M41.85,42.29l-11-.06a.55.55,0,0,0-.36.13L28.58,44H29.9L32,44l11.77.16a.54.54,0,0,0,.55-.53V27.93l-1.65,1.92a.55.55,0,0,0-.13.35L42.4,41.75A.55.55,0,0,1,41.85,42.29Z\"/><path class=\"b\" d=\"M40.25,40.54l-7.62-.05a.54.54,0,0,0-.38.15l-1.64,1.6,11.24.06a.54.54,0,0,0,.55-.54L42.53,30,41,31.74a.54.54,0,0,0-.13.35L40.8,40A.54.54,0,0,1,40.25,40.54Z\"/><path class=\"a\" d=\"M42.41,8.54l-.06,11a.55.55,0,0,0,.13.36l1.62,1.88V20.49c0-.24,0-1,0-2.08.1-6.9.15-10.6.16-11.77a.54.54,0,0,0-.53-.55H28L30,7.74a.55.55,0,0,0,.35.13L41.87,8A.55.55,0,0,1,42.41,8.54Z\"/><path class=\"b\" d=\"M40.66,10.14l-.05,7.62a.54.54,0,0,0,.15.38l1.6,1.64.06-11.24A.54.54,0,0,0,41.88,8L30.12,7.86l1.74,1.49a.54.54,0,0,0,.35.13l7.91.11A.54.54,0,0,1,40.66,10.14Z\"/><path class=\"a\" d=\"M8.15,7.71l11,.06a.55.55,0,0,0,.36-.13L21.42,6H20.1L18,6,6.25,5.82a.54.54,0,0,0-.55.53V22.07l1.65-1.92a.55.55,0,0,0,.13-.35L7.6,8.25A.55.55,0,0,1,8.15,7.71Z\"/><path class=\"b\" d=\"M9.75,9.46l7.62.05a.54.54,0,0,0,.38-.15l1.64-1.6L8.15,7.71a.54.54,0,0,0-.55.54L7.47,20,9,18.26a.54.54,0,0,0,.13-.35L9.2,10A.54.54,0,0,1,9.75,9.46Z\"/><\/svg>',
		'buttonExitFS': '<svg class=\"im-common-exit-fs-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}<\/style><\/defs><path class=\"a\" d=\"M19.86,30.89l-.06,11.5a.57.57,0,0,0,.14.38l1.69,2V43.35c0-.25,0-1.06,0-2.17.1-7.19.15-11.05.17-12.27a.56.56,0,0,0-.55-.57H4.87l2,1.72a.57.57,0,0,0,.36.14l12,.13A.57.57,0,0,1,19.86,30.89Z\"/><path class=\"b\" d=\"M18,32.55,18,40.5a.57.57,0,0,0,.16.4L19.8,42.6l.06-11.72a.57.57,0,0,0-.56-.57L7,30.18l1.82,1.56a.57.57,0,0,0,.36.14l8.25.11A.57.57,0,0,1,18,32.55Z\"/><path class=\"a\" d=\"M31.29,30l11.5.06a.57.57,0,0,0,.38-.14l2-1.69H43.75l-2.17,0-12.27-.17a.56.56,0,0,0-.57.55V45l1.72-2a.57.57,0,0,0,.14-.36l.13-12A.57.57,0,0,1,31.29,30Z\"/><path class=\"b\" d=\"M33,31.85l7.94.05a.57.57,0,0,0,.4-.16L43,30.08,31.29,30a.57.57,0,0,0-.57.56l-.14,12.26L32.14,41a.57.57,0,0,0,.14-.36l.11-8.25A.57.57,0,0,1,33,31.85Z\"/><path class=\"a\" d=\"M30.14,19.11l.06-11.5a.57.57,0,0,0-.14-.38l-1.69-2V6.65c0,.25,0,1.06,0,2.17-.1,7.19-.15,11.05-.17,12.27a.56.56,0,0,0,.55.57H45.13l-2-1.72a.57.57,0,0,0-.36-.14l-12-.13A.57.57,0,0,1,30.14,19.11Z\"/><path class=\"b\" d=\"M32,17.45,32,9.5a.57.57,0,0,0-.16-.4L30.2,7.4l-.06,11.72a.57.57,0,0,0,.56.57L43,19.82l-1.82-1.56a.57.57,0,0,0-.36-.14L32.53,18A.57.57,0,0,1,32,17.45Z\"/><path class=\"a\" d=\"M18.71,20l-11.5-.06a.57.57,0,0,0-.38.14l-2,1.69H6.25l2.17,0,12.27.17a.56.56,0,0,0,.57-.55V5L19.54,7a.57.57,0,0,0-.14.36l-.13,12A.57.57,0,0,1,18.71,20Z\"/><path class=\"b\" d=\"M17,18.15,9.1,18.1a.57.57,0,0,0-.4.16L7,19.92,18.71,20a.57.57,0,0,0,.57-.56l.14-12.26L17.86,9a.57.57,0,0,0-.14.36l-.11,8.25A.57.57,0,0,1,17,18.15Z\"/><\/svg>',
		'buttonZoomIn': '<svg class=\"im-common-zoom-in-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}.c{fill:#fff;}<\/style><\/defs><polygon class=\"a\" points=\"28.91 28.29 44.37 42.74 44.37 40.28 30.11 26.91 28.91 28.29\"/><path class=\"a\" d=\"M31,29a14.44,14.44,0,1,0-1.31,1.31A17.88,17.88,0,0,0,31,29Zm1.78-9.57a12.65,12.65,0,0,1-3.71,9h0A12.66,12.66,0,1,1,20.12,6.75h0A12.66,12.66,0,0,1,32.78,19.41Z\"/><path class=\"b\" d=\"M29.8,27.94a12.74,12.74,0,1,0-1.15,1.16A15.77,15.77,0,0,0,29.8,27.94Zm1.57-8.45a11.16,11.16,0,0,1-3.27,7.9h0A11.17,11.17,0,1,1,20.21,8.33h0A11.17,11.17,0,0,1,31.38,19.5Z\"/><polygon class=\"b\" points=\"29.22 28.43 44.37 42.59 44.37 45.05 27.83 29.63 29.22 28.43\"/><rect class=\"a\" x=\"20.26\" y=\"12.5\" width=\"1.5\" height=\"6.74\"/><rect class=\"c\" x=\"18.76\" y=\"12.5\" width=\"1.5\" height=\"6.74\"/><rect class=\"a\" x=\"21.76\" y=\"19.24\" width=\"5.23\" height=\"1.5\"/><rect class=\"c\" x=\"21.76\" y=\"17.74\" width=\"5.23\" height=\"1.5\"/><rect class=\"a\" x=\"13.52\" y=\"19.24\" width=\"6.74\" height=\"1.5\"/><rect class=\"c\" x=\"13.52\" y=\"17.74\" width=\"6.74\" height=\"1.5\"/><rect class=\"a\" x=\"20.26\" y=\"19.24\" width=\"1.5\" height=\"6.74\"/><rect class=\"c\" x=\"18.76\" y=\"19.24\" width=\"1.5\" height=\"6.74\"/><\/svg>',
		'buttonZoomOut': '<svg class=\"im-common-zoom-out-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}.c{fill:#fff;}<\/style><\/defs><polygon class=\"a\" points=\"28.9 28.28 44.32 42.69 44.32 40.24 30.1 26.9 28.9 28.28\"/><path class=\"a\" d=\"M31,29a14.4,14.4,0,1,0-1.3,1.31A17.83,17.83,0,0,0,31,29Zm1.78-9.55a12.62,12.62,0,0,1-3.7,8.93h0A12.63,12.63,0,1,1,20.13,6.8h0A12.63,12.63,0,0,1,32.76,19.42Z\"/><path class=\"b\" d=\"M29.79,27.93a12.71,12.71,0,1,0-1.15,1.15A15.73,15.73,0,0,0,29.79,27.93Zm1.57-8.42a11.13,11.13,0,0,1-3.27,7.88h0a11.14,11.14,0,1,1-7.87-19h0A11.14,11.14,0,0,1,31.36,19.51Z\"/><polygon class=\"b\" points=\"29.21 28.42 44.32 42.55 44.32 45 27.83 29.62 29.21 28.42\"/><rect class=\"a\" x=\"13.55\" y=\"17.75\" width=\"13.44\" height=\"1.5\"/><rect class=\"c\" x=\"13.55\" y=\"19.25\" width=\"13.44\" height=\"1.5\"/><\/svg>',
		'buttonZoomRestore': '<svg class=\"im-common-zoom-restore-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;}.b{fill:#f6f6f6;}<\/style><\/defs><polygon class=\"a\" points=\"28.9 28.28 44.34 42.71 44.34 40.26 30.11 26.9 28.9 28.28\"/><path class=\"a\" d=\"M31,29a14.42,14.42,0,1,0-1.31,1.31A17.85,17.85,0,0,0,31,29Zm1.78-9.56a12.63,12.63,0,0,1-3.71,8.94h0A12.64,12.64,0,1,1,20.13,6.78h0A12.64,12.64,0,0,1,32.77,19.42Z\"/><path class=\"b\" d=\"M29.8,27.94a12.72,12.72,0,1,0-1.15,1.15A15.75,15.75,0,0,0,29.8,27.94Zm1.57-8.43a11.14,11.14,0,0,1-3.27,7.89h0a11.15,11.15,0,1,1-7.88-19h0A11.15,11.15,0,0,1,31.37,19.5Z\"/><polygon class=\"b\" points=\"29.21 28.42 44.34 42.56 44.34 45.02 27.83 29.63 29.21 28.42\"/><path class=\"a\" d=\"M32.3,22l-7-7.06a.49.49,0,0,0-.31-.14l-2.23-.16.84.84,1.31,1.34,7.39,7.6a.49.49,0,0,0,.69,0l10-10-2.28.18a.49.49,0,0,0-.31.14L33,22A.49.49,0,0,1,32.3,22Z\"/><path class=\"b\" d=\"M32.41,19.89,27.58,15a.49.49,0,0,0-.34-.15l-2.06,0L32.31,22A.49.49,0,0,0,33,22l7.57-7.41-2.06.16a.49.49,0,0,0-.3.14l-5.11,5A.49.49,0,0,1,32.41,19.89Z\"/><\/svg>',
		'borderRadius' : '0px 0px 0px 0px',
		'borderColor' : 'rgba(219, 125, 125, 1) rgba(219, 125, 125, 1) rgba(219, 125, 125, 1) rgba(219, 125, 125, 1)',
		'textColor' : 'rgba(255, 255, 255, 1)',
		'fontFamily' : 'Raleway',
		'fontStyle' : 'normal',
		'fontWeight' : 'normal',
		'fontSize' : '9pt',
		'textAlignment' : 'left',
		'boxColor' : 'transparent',
		'radialBg' : false // Works only in Mozilla Firefox and Google Chrome
	});

	// PopUp
	$.extend(s.imPopUp, {
		'effect' : 'websitex5.bl.project.templates.properties.showboxanimation',
		'width' : 500,
		'shadow' : '',
		'background' : 'rgba(42, 54, 59, 0.9)',
		'backgroundBlur' : false,
		'borderRadius' : 10,
		'textColor' : 'rgba(255, 255, 255, 1)',
		'boxColor' : 'transparent'
	});

	// Tip
	$.extend(s.imTip, {
		'borderRadius' : 0,
		'arrowFilePath' : '',
		'arrowHeight' : 0,
		'arrowWidth' : 0,
		'showArrow' : false,
		'showArrowOnVertex' : false,
		'vertexX' : 0,
		'vertexY' : 0,
		'position' : 'bottomcenter',
		'effect' : 'fade'
	});

	// PageToTop
	$.extend(s.imPageToTop, {
		'imageFile' : 'style/page-to-top.png'
	});

	// Captcha
	b.push(function () {
		x5engine.captcha.instance = new x5engine.captcha.x5captcha({
			"phpfile": "captcha/x5captcha.php",
		});
	}, false, 'first');
	b.push(function() { x.passwordpolicy.init({ "requiredPolicy": "false", "minimumCharacters": "6", "includeUppercase": "false", "includeNumeric": "false", "includeSpecial": "false" });
	});

	// BreakPoints
	s.breakPoints.push({"name": "Desktop", "hash": "ea2f0ee4d5cbb25e1ee6c7c4378fee7b", "start": "max", "end": 1150.0, "fluid": false});
	s.breakPoints.push({"name": "Breakpoint 1", "hash": "d2f9bff7f63c0d6b7c7d55510409c19b", "start": 1149.9, "end": 720.0, "fluid": false});
	s.breakPoints.push({"name": "Mobile", "hash": "72e5146e7d399bc2f8a12127e43469f1", "start": 719.9, "end": 480.0, "fluid": false});
	s.breakPoints.push({"name": "Mobile Fluid", "hash": "5ecdcca63de80fd3d4fbb36295d22b7d", "start": 479.9, "end": 0.0, "fluid": true});

	b.push(function () { x.cookielaw.activateScripts() });

	s.loaded = true;
})( _jq, x5engine );
