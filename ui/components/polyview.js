window.PolyView = Backbone.View.extend({
  template: null,
  model: null,
  events: null,

  _bindings: null,

  /**
   * Set the template to the given html selector
   * @param selector
   */
  setTemplate: function (selector) {
    this.template = _.template(jQuery(selector).html());
  },

  /**
   * Update event bindings for the view
   */
  updateBindings: function () {
    this._bindings = this._bindings || [];

    const currentlyBoundAttrs = this.$el.find('[data-binding]');
    currentlyBoundAttrs.each(this.addBinding.bind(this));
  },

  /**
   * Adds a data binding to the element if supported
   * @param index
   * @param el
   * @returns {*}
   */
  addBinding: function (index, el) {
    switch (el.tagName.toLowerCase()) {
      case 'input':
        return this.bindInput(el);
      default:
        throw "Binding non-inputs is not supported"
    }
  },

  /**
   * Adds a data binding to the given input
   * @param input
   */
  bindInput: function (input) {
    const self = this;
    const event_name = 'change [name=' + input.name + ']';
    if (this.events[event_name]) {
      return;
    }
    const bindingPath = input.getAttribute('data-binding');
    const pathParts = bindingPath.split('.');
    const boundObject = this._resolvePath(pathParts, this);
    const boundProperty = pathParts.pop();

    if (boundObject) {
      boundObject.on('change:' + boundProperty, this.handleBoundDataEvent, this);
    }

    this.events[event_name] = 'handleBoundDOMEvent';
    this.delegate('change', '[name=' + input.name + ']', _.bind(this.handleBoundDOMEvent, this));
    this._bindings[bindingPath] = this._bindings[bindingPath] || [];
    this._bindings[bindingPath].push(input);
  },

  /**
   * Sets the value of the given input
   * @param input
   * @param value
   */
  setInputValue: function (input, value) {
    if (input.type === 'radio') {
      input.checked = (input.value === value);
    } else {
      input.value = value;
    }
  },

  /**
   * Updates the values of the given binding
   * @param path
   * @param value
   */
  updateBoundElements: function (path, value) {
    this._bindings[path].forEach(function (element) {
      element.value = value;
    });
  },

  initialize: function (config) {
    const self = this;
    this._bindings = {};
    this.events = {};

    if (config.template) {
      this.setTemplate(config.template);
    }

    if (config.model) {
      config.model.bind('change', function () {
        self.reRender()
      });
    }

    this.name = config.name;

    if (config.render !== false) {
      this.reRender();
    }
  },

  reRender: function () {
    this.render();
    this.updateBindings();
  },

  render: function (options) {
    const resolvedOptions = options || {};

    resolvedOptions.model = this.model.toJSON();

    if (this.template) {
      this.$el.html(this.template(resolvedOptions));
    }
  },

  setProperty: function (path, value) {
    const pathParts = path.split('.');
    const resolvedPath = this._resolvePath(pathParts, this);
    const options = {};
    if (typeof resolvedPath === 'object') {
      options[pathParts.pop()] = value;
      resolvedPath.set(options, {
        __from: this,
        __ignore: true
      });
    } else {
      throw "Unable to resolve the path of " + path + ", as the path doesn't exist"
    }
  },

  _resolvePath: function (path, target) {
    var newPath = path.concat([]);
    if (newPath.length === 1) {
      return target; // This is the parent
    }

    var nextPath = newPath.shift();
    // Peek at the next element to make sure it isn't null
    if (typeof target[nextPath] === 'object') {
      return this._resolvePath(newPath, target[nextPath]);
    }

    return null;
  },

  handleBoundDOMEvent: function (evt) {
    const boundPropertyPath = evt.target.getAttribute('data-binding');
    this.setProperty(boundPropertyPath, evt.target.value);
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

    this.reRender();
  }
});
