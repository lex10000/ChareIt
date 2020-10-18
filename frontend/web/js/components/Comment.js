class Comments extends React.Component {
    constructor(props) {
        super(props);
        this.createMessage = this.createMessage.bind(this);
        this.getComments = this.getComments.bind(this);
        this.state = {
            'comments': [],
            'isLoaded': false,
            'message': '',
            'validateError': '',
            'isEmpty': false
        };
    }

    componentDidMount() {
        this.getComments();
    }

    getComments(offset = 0) {
        const url = offset !== 0 ? '/chareit/comment/get-comments?postId=' + this.props.postId + '&offset=' + offset : '/chareit/comment/get-comments?postId=' + this.props.postId;
        fetch(url).then(res => res.json()).then(result => {
            if (result.status === 'success') {
                this.setState({
                    'comments': [...this.state.comments, ...result.comments],
                    'isLoaded': true
                });
            } else {
                this.setState({
                    'isEmpty': true,
                    'isLoaded': true
                });
            }
        });
    }
    createMessage(e) {
        e.preventDefault();
        if (this.validateMessage(this.state.message)) {
            fetch('/chareit/comment/create-comment', {
                method: 'POST',
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 'postId': this.props.postId, 'message': this.state.message.trim() })
            }).then(res => res.json()).then(result => {
                if (result.status === 'success') {
                    this.setState({
                        'comments': [...this.state.comments, result.comment],
                        'message': '',
                        'validateError': '',
                        'isEmpty': false
                    });
                } else {
                    this.setState({
                        'validateError': result.status,
                        'message': ''
                    });
                }
            });
        } else {
            this.setState({
                validateError: 'Сообщение не прошло валидацию'
            });
        }
    }

    deleteMessage() {}

    validateMessage(message) {
        //return (message.length > 0 && message.length < 255)
        return true;
    }

    render() {
        if (this.state.isLoaded) {
            if (this.state.isEmpty) {
                return React.createElement(
                    'div',
                    { className: 'comments' },
                    React.createElement(
                        'form',
                        { onSubmit: this.createMessage },
                        React.createElement('input', { type: 'text', name: 'input-message', value: this.state.message, onChange: e => this.setState({ 'message': e.target.value.trim() }) }),
                        React.createElement(
                            'button',
                            { type: 'submit', className: 'btn' },
                            React.createElement(
                                'i',
                                { className: 'material-icons prefix' },
                                'mode_edit'
                            )
                        )
                    ),
                    React.createElement(
                        'div',
                        null,
                        '\u041A\u043E\u043C\u043C\u0435\u043D\u0442\u0430\u0440\u0438\u0435\u0432 \u043D\u0435\u0442'
                    )
                );
            } else {
                return React.createElement(
                    'div',
                    { className: 'comments' },
                    React.createElement(
                        'form',
                        { onSubmit: this.createMessage },
                        React.createElement('input', { type: 'text', name: 'input-message', onChange: e => this.setState({ 'message': e.target.value }) }),
                        React.createElement(
                            'button',
                            { type: 'submit', className: 'btn' },
                            React.createElement(
                                'i',
                                { className: 'material-icons prefix' },
                                'mode_edit'
                            )
                        )
                    ),
                    this.state.validateError && React.createElement(
                        'div',
                        { className: 'validate__error' },
                        this.state.validateError
                    ),
                    this.state.comments.map(comment => {
                        return React.createElement(
                            'div',
                            { className: 'comment', key: comment.id },
                            React.createElement(
                                'a',
                                { href: `/profile/${comment.user_id}`, className: 'comment__avatar circle' },
                                React.createElement('img', { src: `/profile_avatars/thumbnails/${comment.picture}`, alt: '' })
                            ),
                            React.createElement(
                                'div',
                                null,
                                React.createElement(
                                    'a',
                                    { href: `/profile/${comment.user_id}`, className: 'comment__username' },
                                    comment.username
                                ),
                                React.createElement(
                                    'div',
                                    { className: 'comment__message' },
                                    comment.comment
                                ),
                                React.createElement(
                                    'div',
                                    { className: 'comment__date' },
                                    comment.date
                                )
                            )
                        );
                    })
                );
            }
        } else return null;
    }
}
export default Comments;