window.RokSlideshowPath = '';

var myshow;
window.addEvent('load', function(){
	var imgs = [];


	imgs.push({
		file: '1.png',
		title: 'Clear Separation',
		desc: 'PIWI separates your content and logic from the layout.',
		url: '#'
	});
	imgs.push({
		file: '2.png',
		title: 'Features',
		desc: 'PIWI provides some powerful features, that allow you, to develop your website quickly.',
		url: '#'
	});
	imgs.push({
		file: '3.png',
		title: 'Processing',
		desc: 'You create your contents as XML and PIWI takes care of rendering them in various formats like HTML, PDF or XML.',
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