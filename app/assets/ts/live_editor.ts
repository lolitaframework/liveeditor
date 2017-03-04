/// <reference path="jquery.d.ts" />

namespace LolitaFramework {

    class LiveEditor {

        /**
         * Cookies module
         * @type {any}
         */
        Cookies: any = (<any>window).Cookies;

        /**
         * Cookie key name
         * @type {string}
         */
        cookie_name: string = 'live_editor';

        /**
         * Button selector
         * @type {any}
         */
        button: any = '#wp-admin-bar-edit_mode a';

        /**
         * LiveEditor class constructor
         */
        constructor() {
            jQuery(document).on(
                'click',
                this.button,
                (e: any) => this.buttonClick(e)
            );
            document.addEventListener("DOMContentLoaded", () => this.init());
        }

        /**
         * Click event
         * @param {any} e
         */
        buttonClick(e:any) {
            this.toggle();
            e.preventDefault();
        }

        /**
         * Is ON ?
         * @return {boolean} YES / NO
         */
        isON(): boolean {
            return undefined !== this.Cookies.get(this.cookie_name);
        }

        /**
         * Init events
         */
        init() {
            if (this.isON()) {
                this.ON();
            } else {
                this.OFF();
            }
        }

        /**
         * Toggle ON / OFF
         */
        toggle() {
            if (this.isON()) {
                this.OFF();
            } else {
                this.ON();
            }
        }

        /**
         * Edit Mode ON
         */
        ON() {
            jQuery(this.button).parent().addClass('on');
            jQuery(document).trigger('live_editor_enabled');
            this.Cookies.set(this.cookie_name, true, { path: '/' });
        }

        /**
         * Edit Mode OFF
         */
        OFF() {
            jQuery(this.button).parent().removeClass('on');
            jQuery(document).trigger('live_editor_disabled');
            this.Cookies.remove(this.cookie_name, { path: '/' });

        }
    }

    (<any>window).LolitaFramework.liveeditor = new LiveEditor();
}