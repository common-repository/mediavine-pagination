jQuery(document).ready(function () {
  var model = Backbone.Model.extend({
    showPageNumbers: function () {
      return this.get('display_setting') !== 'next';
    },
    showNextButton: function () {
      return this.get('display_setting') !== 'number';
    },
    hoverbg: function () {
      var color = this.get('button_background_color');
      if (this.isColorDark(color)) {
        return this._adjustColor(color, 40);
      }

      return this._adjustColor(color, -20);
    },
    getButtonFontMod: function () {
      const options = this.get('button_font_mod') || '';
      return this._parseFontMod(options);
    },
    getTeaserFontMod: function () {
      const options = this.get('teaser_font_mod') || '';
      return this._parseFontMod(options);
    },
    getButtonMargin: function () {
      return Math.floor(this.get('button_font_size') / 2);
    },
    shouldHideButtons: function () {
      return false;
    },
    getButton: function (text) {
      return this.get('page_number_text').replace(/\{pagenumber\}/gi, text);
    },

    _parseFontMod: function (options) {
      const optionParts = options.split(',').filter(function (option) {
        return option.length;
      });
      var modStyle = '';

      optionParts.forEach(function (option) {
        if (option === 'bold') {
          modStyle += "font-weight: bold;\r\n";
        } else if (option === 'italic') {
          modStyle += "font-style: italic;\r\n";
        } else if (option === 'underline') {
          modStyle += 'text-decoration: underline;\r\n';
        }
      });

      return modStyle;
    },

    _adjustColor: function (hex, steps) {
      var newHex = this.normalizeHex(hex),
        useHash = (hex[0] === '#');

      var {r, g, b} = this.getRGB(newHex);

      r = this._constrain(r + steps, 0, 255);
      b = this._constrain(b + steps, 0, 255);
      g = this._constrain(g + steps, 0, 255);

      newHex = (useHash) ? '#' : '';
      newHex += r.toString(16);
      newHex += g.toString(16);
      newHex += b.toString(16);

      return newHex;
    },

    isColorDark: function (hex) {
      var newHex = this.normalizeHex(hex);

      if (newHex[0] === '#') {
        newHex = newHex.slice(1);
      }

      var {r, g, b} = this.getRGB(newHex);

      var contrast = Math.sqrt(
        r * r * .241 +
        g * g * .691 +
        b * b * .068
      );

      return contrast < 130;
    },

    getRGB(hex){
      var r = '' + hex[0] + hex[1],
        g = '' + hex[2] + hex[3],
        b = '' + hex[4] + hex[5];

      r = parseInt(r, 16);
      b = parseInt(b, 16);
      g = parseInt(g, 16);

      return {
        r, g, b
      };
    },

    normalizeHex: function (hex) {
      var newHex = hex,
        useHash = (newHex[0] === '#');
      if (useHash) {
        newHex = newHex.slice(1);
      }

      if (newHex.length === 3) {
        newHex = '' + newHex[0] + newHex[0] + newHex[1] + newHex[1] + newHex[2] + newHex[2];
      }

      return newHex;
    },

    _constrain: function (value, min, max) {
      var newVal = (value > max) ? max : value;
      newVal = (newVal < min) ? min : newVal;
      return newVal
    }
  });
  window.MediavinePaginationSettingsModel = new model({});
  MediavinePaginationSettingsModel.set(WPModel);
});
