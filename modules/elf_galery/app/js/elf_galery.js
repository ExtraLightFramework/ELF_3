function ELF_Galery(show,cont,galery,params) {
	this._galery = galery; // из какой галерии (по сути модель данных) извлекать изображения
	this._l = 0;// индекс стартового и одновременно левого изображения
	this._r = 0;
	this._next = 0;
	this._prev = 0;
	this._max_idx = 0;
	this._rem_div = 0;
	this._img = [],
	this._calc_w = false;
	this._show = show; // количество видимых изображений
	this._cont = cont; // ИД контейнера галереи
	this._params = params; // параметры для модели данных в json формате
	this._prefix = "ELF_G"+parseInt(Math.random()*10000)+"-";
}

ELF_Galery.prototype = {
	constructor: ELF_Galery,
	
	start: function(type, marg_x, marg_y) {
		// start - номер стартового изображения
		// type - иконка либо большая картинка (icon,image)
		// calc_width - флаг вычисления ширины изображений "на лету"
		this._l = 0;
		this._marg_x = (typeof marg_x != 'undefined')?marg_x:0;
		this._marg_y = (typeof marg_y != 'undefined')?marg_y:0;
		this._shift = parseInt($("#"+this._cont+" div.galery-items").width()); // ширина области для видимых изображений
		this._width = parseInt(this._shift/this._show); // ширина квадрата с изо
		this._height = parseInt($("#"+this._cont).height()); // высота квадрата с изо
		var obj = this;
		$.post("/galery/items",{galery:this._galery,params:JSON.stringify(this._params)},function(data) {
			if (data) {
				var _j = 0;
				while (typeof data[_j] != 'undefined') {
					obj._img[_j] = [];
					obj._img[_j]['src'] = data[_j]['src_'+type];
					obj._img[_j]['orient'] = data[_j]['orient'];
					obj._img[_j]['whc'] = data[_j]['wh_coof'];
					obj._img[_j]['margin-top'] = obj._img[_j]['margin-left'] = 0;
					switch (obj._img[_j]['orient']) {
						case 'vertical':
							obj._img[_j]['height'] = obj._height;
							obj._img[_j]['width'] = parseInt(obj._height*obj._img[_j]['whc']);
							if (obj._img[_j]['width'] < obj._width) {
								obj._img[_j]['margin-left'] = parseInt((obj._width - obj._img[_j]['width'])/2);
							}
							break;
						case 'square':
						case 'horizontal':
							obj._img[_j]['width'] = obj._width;
							obj._img[_j]['height'] = parseInt(obj._width/obj._img[_j]['whc']);
							if (obj._img[_j]['height'] < obj._height) {
								obj._img[_j]['margin-top'] = parseInt((obj._height - obj._img[_j]['height'])/2);
							}
							break;
					}
					if (obj._marg_x) {
						obj._img[_j]['width'] = parseInt(obj._img[_j]['width']-(obj._marg_x*2));
						obj._img[_j]['margin-left'] = parseInt(obj._img[_j]['margin-left']+obj._marg_x);
					}
					if (obj._marg_y) {
						obj._img[_j]['height'] = parseInt(obj._img[_j]['height']-(obj._marg_y*2));
						obj._img[_j]['margin-top'] = parseInt(obj._img[_j]['margin-top']+obj._marg_y);
					}
					_j ++;
				}
				obj._max_idx = _j-1;
				
				var _i = 0;
				obj._l = 0;
				while (_i < obj._show) {
					obj.createElem(_i, _i);
					if (_i == obj._max_idx) {
						break;
					}
					_i ++;
				}
				obj._r = _i - 1;
				obj.recalc();

				if (_i == obj._max_idx)
					$("#"+obj._cont+" div.galery-items i.arr").remove();
			}
			else
				$("#"+obj._cont+" div.galery-items i.arr").remove();
		},'json');
	},
	createElem: function(_i, _shft) {
		$("#"+this._cont+" div.galery-items").append('<div id="'+this._prefix+_i+'" class="galery-item"><img src="'+this._img[_i]['src']+'" alt="" data-pos="'+_i+'" /></div>');
		$("#"+this._prefix+_i).width(this._width);
		$("#"+this._prefix+_i).height(this._height);
		$("#"+this._prefix+_i).css('left',(this._width*_shft)+'px');
		$("#"+this._prefix+_i+" img").width(this._img[_i]['width']);
		$("#"+this._prefix+_i+" img").height(this._img[_i]['height']);
		$("#"+this._prefix+_i+" img").css('margin-top',this._img[_i]['margin-top']+'px');
		$("#"+this._prefix+_i+" img").css('margin-left',this._img[_i]['margin-left']+'px');
	},
	shift: function(direct) {
		var obj = this;
		switch (direct) {
			case 'left':
				this.createElem(this._next,this._show);
				this._rem_div = this._l;
				this._l ++;
				this._r ++;
				break;
			case 'right':
				this.createElem(this._prev,-1);
				this._rem_div = this._r;
				this._l --;
				this._r --;
				break;
		}
		$('#'+this._cont+' div.galery-item').stop().animate({left:(direct=='left'?'-':'+')+'='+this._width+'px'},500,
			function() {$("#"+obj._prefix+obj._rem_div).remove();});
		this.recalc();
	},
	recalc: function() {
		if (this._l>this._max_idx)
			this._l = 0;
		else if (this._l<0)
			this._l = this._max_idx;
		if (this._r>this._max_idx)
			this._r = 0;
		else if (this._r<0)
			this._r = this._max_idx;
		if (this._l-1<0)
			this._prev = this._max_idx;
		else
			this._prev = this._l-1;
		if (this._r+1>this._max_idx)
			this._next = 0;
		else
			this._next = this._r+1;
	}
}
