var _MessageBox = function () {
    if (!(this instanceof _MessageBox)) {
        throw new Error("Calling the _MessageBox constructor as function is forbidden");
    }

    _MessageBox.prototype.messageTag = document.body.querySelector('div.messageContainer');

    _MessageBox.prototype.getTpl = function () {
        var container = document.createElement('div');

        container.innerHTML =   '<div class="messageBox">' +
                                    '<i class="icon-cross js-closeMessage"></i>' +
                                    '<i class="icon"></i>' +
                                    '<p class="message"></p>' +
                                '</div>';

        return container.firstChild;
    };

    _MessageBox.prototype.show = function (type, message, tpl) {
        this.id = 'message_' + Math.random().toString(24).substr(2, 24);
        this.tpl = 'messageBox';
        this.tag = this.getTpl();
        this.tag.classList.add(type);
        this.tag.querySelector('.icon').classList.add('icon-msgBox-' + type);
        this.tag.querySelector('.message').innerHTML = message;
        this.tag.querySelector('.js-closeMessage').setAttribute('onclick', "messagesBoxes['" + this.id + "'].close();");

        messagesBoxes[this.id] = this;

        this.messageTag.appendChild(this.tag);
        this.tag.classList.add('show');

        var self = this;
        setTimeout(function () {
            if (messagesBoxes[self.id]) {
                self.close();
            }
        }, 5000);
    };

    _MessageBox.prototype.close = function () {
        this.tag.classList.add('hide');

        var self = this;
        setTimeout(function () {
            if (self.tag && self.messageTag) {
                self.messageTag.removeChild(self.tag);
                delete messagesBoxes[self.id];
            }
        }, 400);
    };
};

var messagesBoxes = {};
var MessageBox = function (type, message, tpl) {
    (new _MessageBox).show(type, message, tpl);
}
