(($) => {

  $(() => {
    if (window.elementor !== undefined)
      elementor.hooks.addAction('panel/open_editor/widget', (panel = {$el: undefined}, model, view) => {
        Data = {};
        if (panel.$el) {
          init_miqid_filter(panel.$el);
        }
        //Delay('Binding', () => {
        panel.$el.find('.elementor-repeater-fields-wrapper .elementor-repeater-fields').
            on('click', '.elementor-repeater-row-tools .elementor-repeater-tool-duplicate', () => init_miqid_filter(panel.$el));
        panel.$el.
            on('click', 'button.elementor-repeater-add', () => init_miqid_filter(panel.$el));
      });

  });

  const init_miqid_filter = (elem) => {
    let _elements = $(elem).find('.elementor-controls-popover .elementor-control-miqid_filter.elementor-control-type-textarea');
    $.each(_elements, (i, _elem) => {
      let _element = $(_elem);
      _element.removeClass('elementor-control-type-textarea');
      _element = _element.find('.elementor-control-content');
      load_textarea(_element);
    });
    $.each(_elements, (i, _elem) => {
      let _element = $(_elem);
      _element.removeClass('elementor-control-type-textarea');
      _element = _element.find('.elementor-control-content');
      render_miqid_filter(_element);
    });
  };

  const render_miqid_filter = (elem) => {
    let _element = $(elem),
        _textarea = _element.find('textarea'),
        _id = _textarea.attr('id');

    _element.append($(`<div class="wrapper">
    <ul></ul>
    <div class="elementor-button-wrapper" style="padding-top: 5px; text-align: center">
        <button class="elementor-button elementor-button-default add" type="button"><i class="eicon-plus" aria-hidden="true"></i>Tilføj</button>
        <button class="elementor-button elementor-button-default save" type="button"><i class="eicon-save" aria-hidden="true"></i>Gem</button>
    </div>
</div>
<style>
    .elementor-control-miqid_filter { }
    .elementor-control-miqid_filter textarea { display: none; }
    .elementor-control-miqid_filter fieldset { border: 0; border-bottom: 1px solid #c3c4c7; margin-bottom: 10px; }
    .elementor-control-miqid_filter fieldset legend { display: flex !important; width: 100%; padding: 5px 0; font-weight: 900; justify-content: space-between; position: relative; }
    .elementor-control-miqid_filter fieldset legend .btn-group .fas { cursor: pointer; }
    .elementor-control-miqid_filter fieldset legend .btn-group .fas.fa-trash { color: orangered; }
    .elementor-control-miqid_filter fieldset > * { display: none; }
    .elementor-control-miqid_filter fieldset.active > * { display: block; }
</style>`));

    render_fieldset(elem);

    _element.on('change', `select[id*=miqid_filter_class]`, (sel_class) => {
      let _current = $(sel_class.currentTarget),
          _fieldset = _current.parents('fieldset'),
          _pos = _fieldset.data('pos'),
          _data = Data[_id][_pos];

      _data.class = _current.val();
      sync_section(elem, _id, _pos, _data);

      render_miqid_property(_fieldset);
      fetch_miqid_properties(_fieldset, _data);
    });

    _element.on('change', `select[id*=miqid_filter_property]`, (sel_property) => {
      let _current = $(sel_property.currentTarget),
          _fieldset = _current.parents('fieldset'),
          _pos = _fieldset.data('pos'),
          _data = Data[_id][_pos];

      _data.property = _current.val();
      sync_section(elem, _id, _pos, _data);

      _fieldset.find('legend span').text(`${_data.property || ''}: ${_data.match || ''}`);

      fetch_miqid_property_match(_fieldset, _data);
    });

    _element.on('change', `[id*=miqid_filter_match]`, (match) => {
      let _current = $(match.currentTarget),
          _fieldset = _current.parents('fieldset'),
          _pos = _fieldset.data('pos'),
          _data = Data[_id][_pos];

      _data.match = _current.val();
      sync_section(elem, _id, _pos, _data);

      _fieldset.find('legend span').text(`${_data.property || ''}: ${_data.match || ''}`);
    });

    _element.on('click', 'button.add', (btn) => {
      add_section(elem);
    });

    _element.on('click', 'button.save', (btn) => {
      _textarea = _element.find(`textarea#${_id}`);

      _textarea[0].dispatchEvent(new Event('input', {bubbles: true, cancelable: true}));
    });

    _element.on('click', 'fieldset legend .fa-trash', (e) => {
      let _current = $(e.currentTarget),
          _fieldset = _current.parents('fieldset'),
          _pos = _fieldset.data('pos');
      if (confirm('Are you sure?')) {
        delete Data[_id][_pos];
        _fieldset.remove();
        sync_section(elem, _id);
      }
    });

    _element.on('click', 'fieldset legend .fa-angle-down', (e) => $(e.currentTarget).parents('fieldset').toggleClass('active'));
  };

  let Data = {};

  const load_textarea = (elem) => {
    let _element = $(elem),
        _input = _element.find('textarea'),
        _id = _input.attr('id'),
        _val = _input.val();

    if (Data[_id] !== undefined)
      return;

    let _arr = [];
    try {
      _arr = $.extend({}, JSON.parse(_val));
    } catch (e) {

    }

    Data[_id] = _arr;
  };

  const sync_section = (elem, id, pos, data) => {
    let _element = $(elem),
        _textarea = _element.find(`textarea#${id}`);

    if (Data[id][pos] !== undefined)
      Data[id][pos] = data;

    _textarea.val(JSON.stringify(Data[id]));
  };

  const add_section = (elem) => {
    let _element = $(elem),
        _input = _element.find('textarea'),
        _id = _input.attr('id');

    if (Data[_id] === undefined)
      Data[_id] = {};

    let _pos = Object.keys(Data[_id]).length;
    Data[_id][_pos] = {};

    sync_section(elem, _id);
    render_fieldset(elem);
    elem.find('fieldset').last().addClass('active');
  };

  const render_fieldset = (elem) => {
    let _element = $(elem),
        _input = _element.find('textarea'),
        _id = _input.attr('id');

    _element.find('ul').empty();

    $.each(Data[_id], (i, data) => {
      _element.find('ul').append($(`<li>
    <fieldset data-pos="${i}">
        <legend><span>${data.property || ''}: ${data.match || ''}</span>
            <div class="btn-group">
                <i class="fas fa-trash fa-fw"></i>
                <i class="fas fa-angle-down fa-fw"></i>
            </div>
        </legend>
    </fieldset>
</li>`));
      render_miqid_class(elem, i);
      fetch_miqid_classes(elem, i, data);
    });
  };

  const render_miqid_class = (elem, i) => {
    let _fieldset = $(elem).find(`fieldset[data-pos="${i}"]`);
    _fieldset.append($(`<div class="elementor-control elementor-control-miqid_class elementor-control-type-select elementor-label-inline elementor-control-separator-default elementor-control-dynamic"
     style="padding-left: 0; padding-right: 0;">
    <div class="elementor-control-content">
        <div class="elementor-control-field">
            <label for="miqid_filter_class-${i}" class="elementor-control-title">MIQID Category</label>
            <div class="elementor-control-input-wrapper elementor-control-unit-5">
                <select id="miqid_filter_class-${i}">
                    <option value="">Choose...</option>
                </select>
            </div>
        </div>
    </div>
</div>`));
  };

  const fetch_miqid_classes = (elem, i, data) => {
    fetch('/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: new URLSearchParams({action: 'miqid_classes'}),
    }).
        then(resp => resp.json()).
        then(json => {
          let _options = [];
          $.each(json, (cI, _class) => _options.push($(`<option value="${_class.name}">${_class.title}</option>`)));
          elem.find(`select#miqid_filter_class-${i}`).append(_options).val(data.class).trigger('change');
        });
  };

  const render_miqid_property = (fieldset) => {
    let _fieldset = $(fieldset),
        _i = _fieldset.data('pos');

    _fieldset.find(`.elementor-control-miqid_property`).remove();
    if (_fieldset.find(`[id*=miqid_filter_class]`).val().length > 0)
      _fieldset.append($(`<div class="elementor-control elementor-control-miqid_property elementor-control-type-select elementor-label-inline elementor-control-separator-default elementor-control-dynamic"
     style="padding-left: 0; padding-right: 0;">
    <div class="elementor-control-content">
        <div class="elementor-control-field">
            <label for="miqid_filter_property-${_i}" class="elementor-control-title">MIQID Field</label>
            <div class="elementor-control-input-wrapper elementor-control-unit-5">
                <select id="miqid_filter_property-${_i}">
                    <option value="">Choose...</option>
                </select>
            </div>
        </div>
    </div>
</div>`));
  };

  const fetch_miqid_properties = (fieldset, obj) => {
    let _fieldset = $(fieldset),
        _i = _fieldset.data('pos');

    fetch('/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: new URLSearchParams({
        action: 'miqid_properties',
        class: obj.class,
      }),
    }).
        then(resp => resp.json()).
        then(json => {
          let _options = [];
          $.each(json, (pI, _property) => _options.push($(`<option value="${_property.name}">${_property.title}</option>`)));
          _fieldset.find(`select#miqid_filter_property-${_i}`).append(_options).val(obj.property || '').trigger('change');
        });
  };

  const fetch_miqid_property_match = (fieldset, obj) => {
    let _fieldset = $(fieldset),
        _i = _fieldset.data('pos');

    _fieldset.find('.elementor-control-miqid_match').remove();

    if (obj.property !== undefined)
      fetch('/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: new URLSearchParams({
          action: 'miqid_property_match',
          i: _i,
          class: obj.class,
          property: obj.property,
        }),
      }).
          then(resp => resp.json()).
          then(json => {
            _fieldset.append(json.html);
            _fieldset.find(`[id*="miqid_filter_match"]`).val(obj.match || '').trigger('change');
          });

  };

  const Delays = [];
  const Delay = (name, cb, timeout = 250) => {
    clearTimeout(Delays[name || 'Delay']);

    Delays[name || 'Delay'] = setTimeout(cb, timeout);
  };

})(jQuery);