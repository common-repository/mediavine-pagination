// TODO: Merge into single file
jQuery(document).ready(function () {
  window.MediavineUIFontpicker = PolyView.extend({
    template: _.template(jQuery('#mediavine-ui-fontpicker').html()),

    // Params
    name: null,
    value: null,
    modName: null,
    modValue: null,

    // Private Params
    isBold: false,
    isItalic: false,
    isUnderline: false,

    // Computeds
    isBoldClass: function () {
      return (this.isBold ? 'active' : '');
    },

    isItalicClass: function () {
      return (this.isItalic ? 'active' : '');
    },

    isUnderlineClass: function () {
      return (this.isUnderline ? 'active' : '');
    },

    toggleProperty: function (prop) {
      this[prop] = !this[prop];
    },

    // Helpers
    updateModValue: function (name) {
      var mods = [];

      if (this.isBold) {
        mods.push('bold');
      }

      if (this.isItalic) {
        mods.push('italic');
      }

      if (this.isUnderline) {
        mods.push('underline');
      }

      this.modValue = mods.join(',');
    },
    bindDataAttr: function (name) {
      this[name] = this.$el.attr('data-' + name) || '';
    },

    capitalize: function (text) {
      return text.charAt(0).toUpperCase() + text.substr(1);
    },

    initFontMod: function () {
      this.modValue.split(',').forEach(function (mod) {
        var propName = 'is' + this.capitalize(mod);
        this[propName] = true;
      }.bind(this));
    },

    initialize: function (config) {
      ['name', 'value', 'modName', 'modValue'].forEach(this.bindDataAttr.bind(this));

      this.initFontMod();
      if (config.render !== false) {
        this.render();
      }

      MediavineUIFontpicker.__super__.initialize(config);
    },
    render: function () {
      var html = this.template({
        name: this.name || '',
        value: this.value || '',
        modName: this.modName || '',
        modValue: this.modValue || '',
        isBoldClass: this.isBoldClass(),
        isItalicClass: this.isItalicClass(),
        isUnderlineClass: this.isUnderlineClass()
      });
      this.$el.html(html);
      const selectedElement = this.$el.find('option[value="' + this.value + '"]');
      if (selectedElement.length) {
        selectedElement.attr('selected', 'selected');
      }
      this.$el.find('.select2').select2({
        minimumResultsForSearch: Infinity,
        containerCssClass: 'select2-mediavine select2-font',
        templateSelection: function (data, container) {
          if (data.element) {
            jQuery(container).css({fontFamily: data.element.value});

          }
          return data.text;
        },
        templateResult: function (data, container) {
          if (data.element) {
            jQuery(container).css({fontFamily: data.element.value});
          }
          return data.text;
        }
      });
      return this;
    },
    events: {
      'change .mv-select': 'onChangeFamily',
      'click .control-toggle': 'onToggleValue'
    },
    onChangeFamily: function (evt) {
      this.value = evt.target.value;
      this.onSelect.call(this.controller, evt.target.value, this);
    },

    onSelect: function (newVal) {
    },
    onModToggle: function (newVal) {
    },
    onToggleValue: function (evt) {
      const toggleTarget = evt.currentTarget.getAttribute('data-toggle');

      this.toggleProperty(toggleTarget);

      this.updateModValue();

      this.render();

      this.onModToggle.call(this.controller, this.modValue, this);
    }
  });
});
