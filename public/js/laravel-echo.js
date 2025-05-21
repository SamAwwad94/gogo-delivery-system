/**
 * Laravel Echo
 * 
 * This is a simplified version of Laravel Echo for use with Pusher.
 * It provides a compatible API for real-time updates.
 */

(function(global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = global || self, global.Echo = factory());
}(this, function() {
    'use strict';

    /**
     * Echo constructor
     * 
     * @param {Object} options - Configuration options
     */
    function Echo(options) {
        this.options = options;
        this.connector = null;
        this.channels = {};

        if (!this.options.broadcaster) {
            throw new Error('Laravel Echo requires a broadcaster.');
        }

        if (this.options.broadcaster === 'pusher') {
            this.connector = new PusherConnector(this.options);
        } else {
            throw new Error(`Unsupported broadcaster: ${this.options.broadcaster}`);
        }
    }

    /**
     * Get a channel instance by name
     * 
     * @param {String} channel - Channel name
     * @returns {Object} - Channel instance
     */
    Echo.prototype.channel = function(channel) {
        if (!this.channels[channel]) {
            this.channels[channel] = new Channel(channel, this.connector);
        }

        return this.channels[channel];
    };

    /**
     * Get a private channel instance by name
     * 
     * @param {String} channel - Channel name
     * @returns {Object} - Channel instance
     */
    Echo.prototype.private = function(channel) {
        return this.channel(`private-${channel}`);
    };

    /**
     * Get a presence channel instance by name
     * 
     * @param {String} channel - Channel name
     * @returns {Object} - Channel instance
     */
    Echo.prototype.presence = function(channel) {
        return this.channel(`presence-${channel}`);
    };

    /**
     * Leave the given channel
     * 
     * @param {String} channel - Channel name
     * @returns {void}
     */
    Echo.prototype.leave = function(channel) {
        if (this.channels[channel]) {
            this.connector.leave(channel);
            delete this.channels[channel];
        }
    };

    /**
     * Disconnect from the Echo server
     * 
     * @returns {void}
     */
    Echo.prototype.disconnect = function() {
        this.connector.disconnect();
    };

    /**
     * Channel constructor
     * 
     * @param {String} name - Channel name
     * @param {Object} connector - Connector instance
     */
    function Channel(name, connector) {
        this.name = name;
        this.connector = connector;
        this.events = {};
    }

    /**
     * Listen for an event on the channel
     * 
     * @param {String} event - Event name
     * @param {Function} callback - Callback function
     * @returns {Channel} - Channel instance
     */
    Channel.prototype.listen = function(event, callback) {
        this.on(event, callback);
        return this;
    };

    /**
     * Register a callback to be called when an event is received
     * 
     * @param {String} event - Event name
     * @param {Function} callback - Callback function
     * @returns {Channel} - Channel instance
     */
    Channel.prototype.on = function(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }

        this.events[event].push(callback);
        this.connector.listen(this.name, event, callback);
        
        return this;
    };

    /**
     * PusherConnector constructor
     * 
     * @param {Object} options - Configuration options
     */
    function PusherConnector(options) {
        this.options = options;
        this.pusher = null;
        this.channels = {};

        this.connect();
    }

    /**
     * Connect to Pusher
     * 
     * @returns {void}
     */
    PusherConnector.prototype.connect = function() {
        if (typeof Pusher === 'undefined') {
            console.warn('Pusher is not available. Real-time updates will not work.');
            return;
        }

        this.pusher = new Pusher(this.options.key, this.options);
    };

    /**
     * Listen for an event on a channel
     * 
     * @param {String} channel - Channel name
     * @param {String} event - Event name
     * @param {Function} callback - Callback function
     * @returns {void}
     */
    PusherConnector.prototype.listen = function(channel, event, callback) {
        if (!this.pusher) return;

        if (!this.channels[channel]) {
            this.channels[channel] = this.pusher.subscribe(channel);
        }

        this.channels[channel].bind(event, callback);
    };

    /**
     * Leave a channel
     * 
     * @param {String} channel - Channel name
     * @returns {void}
     */
    PusherConnector.prototype.leave = function(channel) {
        if (!this.pusher) return;

        if (this.channels[channel]) {
            this.pusher.unsubscribe(channel);
            delete this.channels[channel];
        }
    };

    /**
     * Disconnect from Pusher
     * 
     * @returns {void}
     */
    PusherConnector.prototype.disconnect = function() {
        if (!this.pusher) return;

        this.pusher.disconnect();
    };

    return Echo;
}));
