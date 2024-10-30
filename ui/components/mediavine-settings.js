jQuery(document).ready(function () {
  const colorPickerFields = [
    'teaser_font_color',
    'button_background_color',
    'button_border_color',
    'button_font_color'
  ];
  const fontFields = [
    'teaser_font_style',
    'button_font_style'
  ];
  const borderStyles = [
    'solid',
    'dashed',
    'dotted',
    'double',
    'groove',
    'ridge',
    'none'
  ];

  window.MediavineSettingsView = PolyView.extend({
    template: _.template(jQuery('#mediavine-settings-view').html()),
    colorPickerComponents: null,
    fontPickerComponents: null,
    borderComponent: null,
    showPageNumbers: true,
    showNextButton: true,
    model: null,

    recalcComputeds: function () {
      this.showPageNumbers = (this.displayValue !== 'next');
      this.showNextButton = (this.displayValue !== 'number');
    },

    initFields: function (list, view, options) {
      const subView = view.extend(options || {});
      return list.map(function (name) {
        return new subView({
          el: options.controller.$('#bb_' + name),
          render: false
        });
      });
    },
    events: {
      'keyup .mv-teaser-text': function (evt) {
        this.model.set('teaser_text', evt.target.value);
      }
    },

    initialize: function (config) {
      const self = this;
      this.colorPickerComponents = [];
      this.fontPickerComponents = [];
      this.borderComponent = [];
      this.render(); // Render for initial structure
      this.updateBindings();

      const currentDisplayOptBtn = this.$el.find('input[data-group=display_options]:checked') || [];
      this.displayValue = (currentDisplayOptBtn[0] ? currentDisplayOptBtn[0].value : 'both');
      this.recalcComputeds();

      this.colorPickerComponents = this.initFields(colorPickerFields, MediavineUIColorpicker, {
        controller: this,
        onSelect: function (newValue, component) {
          this.model.set(component.name.replace('mediavinePagSetting_', ''), newValue);
        },
      });

      this.fontPickerComponents = this.initFields(fontFields, MediavineUIFontpicker, {
        controller: this,
        onSelect: function (newValue, component) {
          this.model.set(component.name.replace('mediavinePagSetting_', ''), newValue);
        },
        onModToggle: function (newValue, component) {
          this.model.set(component.modName.replace('mediavinePagSetting_', ''), newValue);
        }
      });

      this.borderComponent = new MediavineUIDropdown({
        el: self.$('#bb_button_border_style'),
        render: false,
        options: borderStyles,
        controller: self,
        onSelect: function (newValue) {
          this.model.set('button_border_style', newValue);
        }.bind(this)
      });

      this.partials = jQuery.map(this.$el.find('.bb-partial'), function (obj) {
        const partialOptions = {
          el: jQuery(obj),
          template: obj.getAttribute('data-template')
        };
        const modelName = obj.getAttribute('data-model');
        if (modelName) {
          partialOptions.model = self[modelName] || null;
        }

        if (partialOptions.template.charAt(0) !== '#') {
          partialOptions.template = '#' + partialOptions.template;
        }
        return new PolyView(partialOptions);
      });

      this.renderComponents();
      PolyView.__super__.initialize({
        render: false
      });
    },

    render: function (opts) {
      var self = this;
      var html = this.template({
        showPageNumbers: this.showPageNumbers,
        showNextButton: this.showNextButton
      });
      this.$el.html(html);

      return this;
    },

    renderComponents: function () {

      this.colorPickerComponents.forEach(function (view) {
        view.render();
      });

      this.fontPickerComponents.forEach(function (view) {
        view.render();
      });

      this.borderComponent.render();
    },
    handleBoundDataEvent: function (model, value, options) {
      const changedAttributes = model.changed;

      if (!(options && options.__from === this && options.__ignore)) {
        _.each(model.changed, function (changeValue, changeKey) {
          const boundElements = this._bindings['model.' + changeKey] || [];
          boundElements.forEach(function (element) {
            // this.setInputValue(element, changeValue);
          }.bind(this));
        }.bind(this));
      }
    }
  });

  const settingsView = new MediavineSettingsView({
    el: '#settings_wrapper',
    model: window.MediavinePaginationSettingsModel
  });
});
