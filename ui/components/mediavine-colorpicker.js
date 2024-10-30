// TODO: Merge into single file
jQuery(document).ready(function () {
  window.MediavineUIColorpicker = PolyView.extend({
    template: _.template(jQuery('#mediavine-ui-colorpicker').html()),
    id: null,
    name: null,
    value: null,
    colorHex: null,
    bindDataAttr: function (name) {
      this[name] = this.$el.attr('data-' + name) || '';
    },
    initialize: function (config) {
      _.each(['id', 'name', 'value'], this.bindDataAttr.bind(this));
      this.recalcColorHex();
      if (config.render !== false) {
        this.render();
      }
      MediavineUIColorpicker.__super__.initialize(config);
    },
    events: {
      'click .color-block': 'onColorBlockClick',
      'change .mv-color-text': 'onChange',
    },
    onSelect: function () {
    },
    onChange: function (evt) {
      this.onSelect.call(this.controller, evt.target.value, this);
    },
    render: function () {
      var html = this.template({
        id: this.id || '',
        name: this.name || '',
        value: this.value || '',
        colorHex: this.colorHex || '',
      });
      this.$el.html(html);

      this.picker = new jscolor(this.$el.find('input')[0], {
        styleElement: this.id + 'color-block',
        hash: true
      });
      return this;
    },
    recalcColorHex: function () {
      this.colorHex = (this.value.charAt(0) === '#' ? '' : '#') + this.value;
    },
    onColorBlockClick: function (evt) {
      this.picker.show();
    }
  });

});
