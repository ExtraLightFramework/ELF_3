class ELF_Galery { // ELF Galery v 2.0
	constructor(cont, request, params) {
		// cont - ИД контейнера для изображений галереи
		// request - URI запроса загрузки изображений для галереи
		//			запрос должен возвращать JSON-массив объектов - [{src:'/path/to/img',alt:'alt text for img'},{...},...]
		//			данные передаются в метод init в параметре resp
		// params - JSON параметры доп. настроек
		//		params.duration - скорость анимации в мс.
		//		params.cnt - количество изображений отображаемых в контейнере
		//		params.pad_y, params.pad_x - отступы от границ контейнера (pad_y) и друг от друга (pad_x)
		//		params.class - дополнительное имя класса для контейнера
		//		params.imgs - JSON-массив объектов изображение (см. параметр request)
		try {
			if (!request && !params.imgs)
				throw new Error('Parameter REQUEST or PARAMS.IMGS are empty');
			this.root = document.getElementById(cont);
			this.root.classList.add('elf-galery-container');
			this.root_imgs = document.createElement('div');
			this.root_imgs.classList.add('elf-galery-container-imgs');
			this.root.append(this.root_imgs);
			this.cnt = 0;
			this.ptr = 0;
			this.obj = [];

			this.DURATION = params && typeof params.duration != 'undefined'?parseInt(params.duration):1000;
			this.PAD_X = params && typeof params.pad_x != 'undefined'?parseInt(params.pad_x):0;
			this.PAD_Y = params && typeof params.pad_y != 'undefined'?parseInt(params.pad_y):0;
			this.SHOW_CNT = params && typeof params.cnt != 'undefined'?parseInt(params.cnt):1;
			this.ADD_CLASS = params && typeof params.class != 'undefined'?params.class:'';
			
			if (this.ADD_CLASS)
				this.root.classList.add(this.ADD_CLASS);
			
			// ===== Big Img Cont Init
			this.bigimg = document.createElement('div');
			this.bigimg.classList.add('elf-galery-big-img');
			let closebigimg = document.createElement('i');
			closebigimg.classList.add('close-bigimg','fas','fa-times');
			closebigimg.addEventListener('click', () => this.closeBigImg());
			this.bigimg.append(closebigimg);
			this.bigimg.img = document.createElement('img');
			this.bigimg.img.addEventListener('load', () => this.onLoadBigImg());
			this.bigimg.append(this.bigimg.img);
			document.body.append(this.bigimg);
			// ===== Next Prev Buttons Init
			let btn = document.createElement('div');
			btn.classList.add('elf-galery-navigate','elf-galery-navigate-next');
			btn.innerHTML = '<i class="fas fa-chevron-right"></i>';
			btn.addEventListener('click', () => this.rotate('left'));
			this.root.append(btn);
			btn = document.createElement('div');
			btn.classList.add('elf-galery-navigate','elf-galery-navigate-prev');
			btn.innerHTML = '<i class="fas fa-chevron-left"></i>';
			btn.addEventListener('click', () => this.rotate('right'));
			this.root.append(btn);
			
			let wh = getComputedStyle(this.root);
			this.WIDTH = wh.width?parseInt(wh.width):0;
			this.HEIGHT = wh.height?parseInt(wh.height):0;
			if (this.WIDTH)
				this.SHIFT = this.WIDTH / this.SHOW_CNT;
			else
				this.SHIFT = 0;
			
			if (request) {
				this.xhr = ELF_Galery.getXhrObject();
				this.xhr.addEventListener('readystatechange', () => this.xhrStateChange());
				this.xhr.addEventListener('load', () => this.xhrResponse());
				
				this.xhrCallback = this.init;
				this.xhr.open('POST', request, true);
				this.xhr.send();
			}
			else
				this.init(this, params.imgs);
		}
		catch(err) {
			console.log('ELF_Galery load error: '+err.message+' Stack: '+err.stack);
		}
	}
	init(obj, resp) {
		if (resp.length) {
			resp.forEach(function(item) {
				obj.obj[obj.cnt] = document.createElement('div');
				obj.obj[obj.cnt].classList.add('elf-galery-item');
				let img = document.createElement('img');
				img.src = item.src;
				img.alt = item.alt;
				
				
				obj.obj[obj.cnt].style.width = (obj.WIDTH / obj.SHOW_CNT)+'px';
				obj.obj[obj.cnt].style.height = obj.HEIGHT+'px';
				
//				img.style.position = 'absolute';
				img.style.left = obj.PAD_X+'px';
//				img.style.top = obj.PAD_Y+'px';
				img.addEventListener('click', function() {obj.showBigImg(this)});
				obj.obj[obj.cnt].append(img);

				img.style.width = (parseInt(obj.obj[obj.cnt].style.width) - obj.PAD_X*2)+'px';
//				img.style.height = (parseInt(obj.obj[obj.cnt].style.height) - obj.PAD_Y*2)+'px';

				if (obj.cnt < obj.SHOW_CNT)
					obj.obj[obj.cnt].style.left = (obj.cnt*parseInt(obj.obj[obj.cnt].style.width))+'px';
				else
					obj.obj[obj.cnt].style.left = obj.WIDTH+'px';
				
				
				obj.root_imgs.append(obj.obj[obj.cnt]);
				obj.cnt ++;
			});
		}
		
	}
	rotate(direct) {
		if (this.cnt > 1) {
			let _set;
			let _shft;
			let _next;
			switch(direct) {
				case 'left':
					_next = this.ptr+this.SHOW_CNT >= this.cnt?this.ptr+this.SHOW_CNT-this.cnt:this.ptr+this.SHOW_CNT;
					_set = this.WIDTH+'px';
					_shft = '-='+(this.WIDTH/this.SHOW_CNT);
					break;
				case 'right':
					_next = this.ptr - 1 < 0?this.cnt-1:this.ptr - 1;
					_set = '-'+(this.WIDTH/this.SHOW_CNT)+'px';
					_shft = '+='+(this.WIDTH/this.SHOW_CNT)+'px';
					break;
			}
			let i = this.ptr;
			let j = 0;
			for (let j = 0;j < this.SHOW_CNT; j ++) {
				let _jq1 = $(this.obj[i]);
				_jq1.stop().animate({left:_shft},this.DURATION);
				i = i+1 >= this.cnt?0:i+1;
			}
			let _jq2 = $(this.obj[_next]);
			_jq2.stop().css({left:_set}).animate({left:_shft},this.DURATION);
			switch(direct) {
				case 'right':
					this.ptr = _next;
					break;
				case 'left':
					this.ptr = this.ptr+1>=this.cnt?0:this.ptr+1;
					break;
			}
		}
	}
	onLoadBigImg() {
		//console.log(this.bigimg.img.width+' '+this.bigimg.img.height);
	}
	showBigImg(img, obj) {
		this.bigimg.style.display = 'block';
		this.bigimg.img.src = img.getAttribute('src');
		let _w = parseInt(this.bigimg.getBoundingClientRect().width);
		let _h = parseInt(this.bigimg.getBoundingClientRect().height);
		let _imgw = parseInt(this.bigimg.img.width);
		let _imgh = parseInt(this.bigimg.img.height);
		let _self = this;
		setTimeout(function() {
		if (_w >= _h) {
			if (_imgw >= _imgh) {// horizontal
				_self.bigimg.img.style.width = '70%';
				_self.bigimg.img.style.left = '15%';
				_imgh = parseInt(_self.bigimg.img.height);
				_self.bigimg.img.style.top = (_h/2-_imgh/2)+'px';

			}
			else {
				_self.bigimg.img.style.height = (_h/2)+'px';
				_self.bigimg.img.style.top = (_h/4)+'px';
				_imgw = parseInt(_self.bigimg.img.width);
				_self.bigimg.img.style.left = ((_w-_imgw)/2)+'px';
			}
		}
		else {
			if (_imgw >= _imgh) {// horizontal
				_self.bigimg.img.style.width = (_w-20)+'px';
				_self.bigimg.img.style.left = '10px';
				_imgh = parseInt(_self.bigimg.img.height);
				_self.bigimg.img.style.top = (_h/2-_imgh/2)+'px';
			}
			else {
				_self.bigimg.img.style.height = (_h-100)+'px';
				_self.bigimg.img.style.top = '50px';
				_imgw = parseInt(_self.bigimg.img.width);
				_self.bigimg.img.style.left = ((_w-_imgw)/4)+'px';
			}
		}}, 1000);
	}
	closeBigImg() {
		this.bigimg.style.display = 'none';
	}
	// COMMON
	static getXhrObject() {
		if(typeof XMLHttpRequest === 'undefined'){
			XMLHttpRequest = function() {
				try {
					return new window.ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(err) {
					alert('ELF_Galery.getXhrObject error: Can not create XMLHttp object');
				}
			}
		}
		return new XMLHttpRequest();
	}
	xhrStateChange() {
		if (this.xhr.readyState == 1) { // .open Method called
			this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		}
	}
	xhrResponse() {
		try {
			if (this.xhr.readyState == 4
				&& this.xhr.status == 200) {
				let resp;
				if (resp = ELF_Galery.getJson(this.xhr.response || this.xhr.responseText, true)) {
					if (resp && resp.error) {
						alert(resp.error);
					}
					else if (this.xhrCallback) {
						this.xhrCallback(this, resp);
					}
				}
				this.xhrCallback = null;
			}
		}
		catch (err) {
			alert('ELF_Galery.xhrResponse error: '+err.message);
		}
	}
	static getJson(str, showerr) {
		let ret;
		if (str) {
			try {
				ret = JSON.parse(str);
			} catch (err) {
				if (showerr) {
					alert('ELF_Galery.getJson error: '+err.message+' '+str.substr(0, 100)+'...');
					ret = false;
				}
				ret = true;
			}
		}
		else
			ret = true;
		return ret;
	}
}