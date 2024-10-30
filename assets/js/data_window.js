(($) => {

    const _body_class = 'elementor_data_window_body_wrapper';
    const _button_class = 'elementor_data_window_button_wrapper';
    let firstRun = true;

    const Data_Window = (element) => {

        $(document).ready(() => {
            let _body = $('body'),
                _element = $(element),
                _wp_post = _element.parents('[data-elementor-type=wp-post]')

            if (_body.hasClass('elementor-editor-active'))
                return;

            if (firstRun) {
                firstRun = false;
                _body.css({display: 'flex'});

                $('body>*').wrapAll(`<div class="${_body_class}" />`);

                _body.append(`<div class="${_button_class}">
    <div class="button-wrapper" data-position="right"></div>
    <div class="button-wrapper" data-position="bottom"></div>
    <div class="button-wrapper" data-position="left"></div>
    <div class="button-wrapper" data-position="top"></div>
</div>`)
            }

            _body.addClass(`elementor-${_wp_post.attr('data-elementor-id')}`)

            _body.append(_element)

            let _body_button_wrapper = _body.find(`.${_button_class}`),
                _button_wrapper = _element.find('.button-wrapper')

            _body_button_wrapper
                .find(`>[data-position="${_button_wrapper.attr('data-position')}"]`)
                .append(_button_wrapper.find('>button'))

            _element
                .off('click')
                .on('click', '.button-close', (e) => btnClose(e))

            _body_button_wrapper
                .off('click')
                .on('click', '.button-open', (e) => btnOpen(e))
        });

        const btnOpen = (e) => {
            let _current = $(e.currentTarget),
                _dataTarget = _current.attr('data-target'),
                _target = $(`.elementor-widget-miqid_elementor_widget_data_window[data-id="${_dataTarget}"]`),
                _wrapper = _target.find('.wrapper'),
                _position = _wrapper.attr('data-position')

            _current.hide();
            switch (_position) {
                case 'right':
                case 'left':
                    _target
                        .css({height: 'auto', order: (_position == 'left' ? 5 : 15)})
                        .animate({
                            width: '25%',
                        }, 1500)
                    break;
            }
        }

        const btnClose = (e) => {
            let _current = $(e.currentTarget),
                _dataTarget = _current.attr('data-target'),
                _target = $(`.elementor-widget-miqid_elementor_widget_data_window[data-id="${_dataTarget}"]`),
                _wrapper = _target.find('.wrapper'),
                _position = _wrapper.attr('data-position')

            switch (_position) {
                case 'right':
                case 'left':
                    _target
                        .animate({
                            width: '0',
                        }, 1500, () => {
                            _target.removeAttr('style')
                            $(`.button-open[data-target="${_dataTarget}"]`).show()
                        })
                    break;
            }
        }

    }

    $(window).on('elementor/frontend/init',
        () => elementorFrontend.hooks.addAction('frontend/element_ready/miqid_elementor_widget_data_window.default', Data_Window)
    )

})(jQuery);