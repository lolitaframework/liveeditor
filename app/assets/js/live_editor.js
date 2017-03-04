var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
var LolitaFramework;
(function (LolitaFramework) {
    var LiveEditor = (function () {
        function LiveEditor() {
            var _this = this;
            this.Cookies = window.Cookies;
            this.cookie_name = 'live_editor';
            this.button = '#wp-admin-bar-edit_mode a';
            jQuery(document).on('click', this.button, function (e) { return _this.buttonClick(e); });
            document.addEventListener("DOMContentLoaded", function () { return _this.init(); });
        }
        LiveEditor.prototype.buttonClick = function (e) {
            this.toggle();
            e.preventDefault();
        };
        LiveEditor.prototype.isON = function () {
            return undefined !== this.Cookies.get(this.cookie_name);
        };
        LiveEditor.prototype.init = function () {
            if (this.isON()) {
                this.ON();
            }
            else {
                this.OFF();
            }
        };
        LiveEditor.prototype.toggle = function () {
            if (this.isON()) {
                this.OFF();
            }
            else {
                this.ON();
            }
        };
        LiveEditor.prototype.ON = function () {
            jQuery(this.button).parent().addClass('on');
            jQuery(document).trigger('live_editor_enabled');
            this.Cookies.set(this.cookie_name, true, { path: '/' });
        };
        LiveEditor.prototype.OFF = function () {
            jQuery(this.button).parent().removeClass('on');
            jQuery(document).trigger('live_editor_disabled');
            this.Cookies.remove(this.cookie_name, { path: '/' });
        };
        return LiveEditor;
    }());
    window.LolitaFramework.liveeditor = new LiveEditor();
})(LolitaFramework || (LolitaFramework = {}));
var LolitaFramework;
(function (LolitaFramework) {
    var Toolbar = (function () {
        function Toolbar() {
            var _this = this;
            this.frame = null;
            jQuery(document).on('live_editor_enabled', function () { return _this.ON(); });
            jQuery(document).on('live_editor_disabled', function () { return _this.OFF(); });
        }
        Toolbar.prototype.removeAction = function (e) {
            var promise = null;
            if (confirm('Are you sure you want to permanently delete this post?')) {
                promise = window.wp.ajax.post({
                    action: 'remove_post',
                    nonce: window.lolita_framework.LF_NONCE,
                    postId: this.getID()
                });
                promise.done(function (response) {
                    window.location.reload();
                });
            }
            e.preventDefault();
        };
        Toolbar.prototype.imageAction = function (e) {
            this.frame.open();
            e.preventDefault();
        };
        Toolbar.prototype.imageActionSelect = function (e) {
            var promise = null;
            promise = window.wp.ajax.post({
                action: 'update_featured_image',
                nonce: window.lolita_framework.LF_NONCE,
                postId: this.getID(),
                attachmentId: this.frame.state().get('selection').first().id
            });
            promise.done(function (response) {
                window.location.reload();
            });
        };
        Toolbar.prototype.ON = function () {
            var _this = this;
            if (this.getID() !== undefined) {
                jQuery('#wp-admin-bar-featured_image, #wp-admin-bar-remove, #wp-admin-bar-date, #wp-admin-bar-tags, #wp-admin-bar-categories').fadeIn();
                this.frame = window.wp.media({
                    frame: 'select',
                    multiple: false,
                    title: 'Insert media',
                    button: {
                        text: 'Insert',
                        close: true
                    }
                });
                this.frame.on('select', function (e) { return _this.imageActionSelect(e); });
                jQuery(document).on('click', '#wp-admin-bar-remove a', function (e) { return _this.removeAction(e); });
                jQuery(document).on('click', '#wp-admin-bar-featured_image a', function (e) { return _this.imageAction(e); });
                jQuery('.open-popup-link a').magnificPopup({
                    type: 'inline',
                    midClick: true,
                    closeBtnInside: false,
                    callbacks: {
                        open: function () {
                            jQuery('#post_date').val(jQuery('#wpadminbar').data('le_date'));
                            window.post_date_flatpickr.setDate(jQuery('#wpadminbar').data('le_date'));
                        }
                    }
                });
                window.post_date_flatpickr = jQuery('#post_date').flatpickr({
                    enableTime: true
                });
                jQuery(document).on('click', '.mfp-clost', function (e) {
                    if (jQuery.magnificPopup !== undefined) {
                        jQuery.magnificPopup.close();
                    }
                    e.preventDefault();
                });
                jQuery(document).on('click', '#button_date_save', function (e) { return _this.dateSave(e); });
            }
        };
        Toolbar.prototype.dateSave = function (e) {
            var _this = this;
            var promise = null;
            promise = window.wp.ajax.post({
                html: jQuery('#post_date').val(),
                data: { postId: jQuery('#wpadminbar').data('le_id') },
                nonce: window.lolita_framework.LF_NONCE,
                action: 'save_date'
            });
            promise.always(function () { return _this.dateSaveAlways(); });
            if (jQuery.magnificPopup !== undefined) {
                jQuery.magnificPopup.close();
            }
            e.preventDefault();
        };
        Toolbar.prototype.dateSaveAlways = function () {
            window.location.reload();
        };
        Toolbar.prototype.OFF = function () {
            jQuery('#wp-admin-bar-featured_image, #wp-admin-bar-remove, #wp-admin-bar-date, #wp-admin-bar-tags, #wp-admin-bar-categories').fadeOut();
            jQuery(document).off('click', '#wp-admin-bar-remove a');
            jQuery(document).off('click', '#wp-admin-bar-featured_image a');
            jQuery(document).off('click', '.mfp-clost');
            jQuery(document).off('click', '#button_date_save');
        };
        Toolbar.prototype.getID = function () {
            return jQuery('#wpadminbar').data('le_id');
        };
        Toolbar.prototype.set = function (id, title, date) {
            jQuery('#wpadminbar').data('le_id', id);
            jQuery('#wpadminbar').data('le_title', title);
            jQuery('#wpadminbar').data('le_date', date);
            this.ON();
        };
        return Toolbar;
    }());
    LolitaFramework.Toolbar = Toolbar;
    window.LolitaFramework.toolbar = new Toolbar();
})(LolitaFramework || (LolitaFramework = {}));
var LolitaFramework;
(function (LolitaFramework) {
    var ContentEditable = (function () {
        function ContentEditable(class_selector) {
            var _this = this;
            this.class_selector = '';
            this.data = window.live_editor;
            this.timeout = undefined;
            this.class_selector = class_selector;
            jQuery(document).on('live_editor_enabled', function () { return _this.ON(); });
            jQuery(document).on('live_editor_disabled', function () { return _this.OFF(); });
        }
        ContentEditable.prototype.ON = function () {
            var me = this;
            jQuery(this.class_selector).each(function () {
                var _this = this;
                this.contentEditable = true;
                this.onkeydown = function (event) {
                    var current = this;
                    current.data_old_HTML = current.innerHTML;
                    if (typeof me.timeout === 'number') {
                        clearTimeout(me.timeout);
                    }
                    me.timeout = setTimeout(function () {
                        current.onchange();
                    }, 500);
                };
                this.onfocus = function (e) {
                    if (e.target.dataset.postId !== undefined) {
                        window.LolitaFramework.toolbar.set(e.target.dataset.postId, jQuery('.live-editor-title-' + e.target.dataset.postId + ':eq(0)').text(), e.target.dataset.postDate);
                    }
                };
                this.onchange = function () { return me.saveData(_this.innerHTML, _this.dataset); };
            });
        };
        ContentEditable.prototype.OFF = function () {
            jQuery(this.class_selector).each(function () {
                this.contentEditable = false;
                this.onfocus = null;
                this.onblur = null;
                this.onchange = null;
            });
        };
        ContentEditable.prototype.saveData = function (html, data) {
            var _this = this;
            var promise = null;
            promise = window.wp.ajax.post({
                html: html,
                data: data,
                nonce: window.lolita_framework.LF_NONCE,
                action: this.getAction()
            });
            promise.fail(function (response) { return _this.failSaving(response); });
        };
        ContentEditable.prototype.failSaving = function (response) {
            alert("Sorry you can't edit this post!");
        };
        ContentEditable.prototype.getAction = function () {
            return 'save_' + this.class_selector.replace('.live-editor-', '');
        };
        return ContentEditable;
    }());
    LolitaFramework.ContentEditable = ContentEditable;
})(LolitaFramework || (LolitaFramework = {}));
var LolitaFramework;
(function (LolitaFramework) {
    var Content = (function (_super) {
        __extends(Content, _super);
        function Content() {
            _super.apply(this, arguments);
        }
        Content.prototype.ON = function () {
            var me = this;
            jQuery(this.class_selector).each(function () {
                var _this = this;
                this.contentEditable = true;
                this.onkeydown = function (event) {
                    var current = this;
                    current.data_old_HTML = current.innerHTML;
                    if (typeof me.timeout === 'number') {
                        clearTimeout(me.timeout);
                    }
                    me.timeout = setTimeout(function () {
                        current.onchange();
                    }, 500);
                };
                this.onfocus = function (e) {
                    this.data_old_HTML = this.innerHTML;
                    if (e.target.dataset.postId !== undefined) {
                        window.LolitaFramework.toolbar.set(e.target.dataset.postId, jQuery('.live-editor-title-' + e.target.dataset.postId + ':eq(0)').text(), e.target.dataset.postDate);
                    }
                };
                this.onblur = function () {
                    if (this.data_old_HTML != this.innerHTML) {
                        this.onchange();
                    }
                };
                this.onchange = function () { return me.saveData(_this.innerHTML, _this.dataset); };
            });
            window.tinyMCE.init({
                selector: this.class_selector,
                inline: true,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste'
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            });
        };
        Content.prototype.OFF = function () {
            _super.prototype.OFF.call(this);
        };
        return Content;
    }(LolitaFramework.ContentEditable));
    window.LolitaFramework.content = new Content('.live-editor-content');
})(LolitaFramework || (LolitaFramework = {}));
var LolitaFramework;
(function (LolitaFramework) {
    var Title = (function (_super) {
        __extends(Title, _super);
        function Title() {
            _super.apply(this, arguments);
        }
        Title.prototype.ON = function () {
            var me = this;
            _super.prototype.ON.call(this);
            window.tinyMCE.init({
                selector: this.class_selector,
                inline: true,
                toolbar: 'undo redo',
                menubar: false
            });
            jQuery(this.class_selector).each(function () {
                if (this.parentElement.tagName == 'A') {
                    this.parentElement.onclick = function (event) {
                        event.preventDefault();
                    };
                }
            });
        };
        Title.prototype.OFF = function () {
            _super.prototype.OFF.call(this);
            jQuery(this.class_selector).each(function () {
                if (this.parentElement.tagName == 'A') {
                    this.parentElement.onclick = function (event) {
                    };
                }
            });
        };
        return Title;
    }(LolitaFramework.ContentEditable));
    window.LolitaFramework.title = new Title('.live-editor-title');
})(LolitaFramework || (LolitaFramework = {}));
//# sourceMappingURL=live_editor.js.map