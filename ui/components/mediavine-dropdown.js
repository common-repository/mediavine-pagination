// TODO: Merge into single file
jQuery(document).ready(function () {
  window.MediavineUIDropdown = PolyView.extend({
    template: _.template(jQuery('#mediavine-ui-dropdown').html()),

    // Params
    name: null,
    value: null,
    options: null,
    labelPath: null,
    valuePath: null,
    mappedOptions: function () {
      var self = this;
      return this.options.map(function (option) {
        return {
          label: self.resolveProperty(self.labelPath, option),
          value: self.resolveProperty(self.valuePath, option)
        };
      });
    },

    resolveProperty: function (path, target) {
      const resolvedPath = path.replace(/model\.?/i, '').split('.');
      if (!resolvedPath[0]) {
        resolvedPath.shift();
      }

      if (!resolvedPath.length) {
        return target;
      }

      return this._resolvePath(resolvedPath, target)[resolvedPath[resolvedPath.length - 1]] || null;
    },

    // Computeds
    // Helpers
    bindDataAttr: function (name) {
      this[name] = this.$el.attr('data-' + name) || '';
    },

    initialize: function (config) {
      ['name', 'value'].forEach(this.bindDataAttr.bind(this));
      this.labelPath = config.labelPath || 'model';
      this.valuePath = config.valuePath || 'model';
      this.options = config.options || [];

      if (config.render !== false) {
        this.render();
      }

      if (config.onSelect) {
        this.onSelect = config.onSelect;
      }
      MediavineUIDropdown.__super__.initialize(config);
    },
    render: function () {
      var html = this.template({
        name: this.name || '',
        value: this.value || '',
      });
      this.$el.html(html);
      const selectContainer = this.$el.find('select');
      this.mappedOptions().forEach(function (option) {
        selectContainer.append('<option value=' + option.value + '>' + option.label + '</option>');
      });
      const selectedElement = this.$el.find('option[value="' + this.value + '"]');
      if (selectedElement.length) {
        selectedElement.attr('selected', 'selected');
      }
      this.$el.find('.select2').select2({
        minimumResultsForSearch: Infinity,
        containerCssClass: 'select2-mediavine'
      });
      return this;
    },
    events: {
      'change .mv-select': 'onChange',
    },
    onChange: function (evt) {
      var newVal = evt.target.value;
      this.onSelect(newVal);
    },

    // Public API
    onSelect: function (newValue) {

    }
  });
});
