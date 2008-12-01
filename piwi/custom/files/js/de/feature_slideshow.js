window.RokSlideshowPath = '';

var myshow;
window.addEvent('load', function(){
	var imgs = [];


	imgs.push({
		file: '1.png',
		title: 'Klare Trennung',
		desc: 'PIWI trennt Ihre Seiteninhalte und Logik von der Darstellung.',
		url: '#'
	});
	imgs.push({
		file: '2.png',
		title: 'Features',
		desc: 'PIWI stellt Ihnen mehrere nützlich Features zur Verfügung, die Ihnen eine schnelle Entwicklung ermöglichen.',
		url: '#'
	});
	imgs.push({
		file: '3.png',
		title: 'Seitenerzeugung',
		desc: 'Sie erstellen Ihre Inhalte als XML und PIWI übernimmt die Darstellung in verschiedenen Formaten wie HTML, PDF oder XML.',
		url: '#'
	});
	myshow = new Slideshow('slideshow', { 
		type: 'flat',
		showTitleCaption: 1,
		captionHeight: 55,
		width: 400, 
		height: 300, 
		pan: 20,
		zoom: 30,
		loadingDiv: 1,
		resize: true,
		duration: [1000, 10000],
		transition: Fx.Transitions.Expo.easeOut,
		images: imgs, 
		path: 'custom/files/images/'
	});
});