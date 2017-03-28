humhub.module('tracker', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;

    var Issue = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Issue, Widget);

    module.export({
        Issue: Issue
    });

});
