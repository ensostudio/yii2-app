'use strict';
/**
 * @property {boolean} debug
 * @property {object} messages the I18n messages
 * @property {function|null} onInit
 */
export class Application
{
    /**
     * @param {object} the configuration settings
     */
    constructor(config)
    {
        this.debug = false;
        this.messages = {};
        this.onInit = null;

        for (let name in config) {
            this[name] = config[name];
        }

        if (typeof this.onInit === 'function') {
            this.onInit.call(this);
        }

        // window.yii.reloadableScripts.push('/site/*');
    }
    /**
     * Returns the translated message.
     *
     * @param {string} message the source message/template
     * @param {object|array} values the values to interpolate
     * @param {boolean} translateValues
     * @return {string}
     */
    translate(message, values = {}, translateValues = false)
    {
        if (this.messages[message]) {
            message = this.messages[message];
        }
        for (let [key, value] of Object.entries(values)) {
            if (translateValues && typeof value === 'string' && this.messages[value]) {
                value = this.messages[value];
            }
            message = message.replace(new RegExp('{' + key + '}'), value);
        }

        return message;
    }
}
