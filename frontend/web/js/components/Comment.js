class Comment extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            'comments': []
        };
    }

    componentDidMount() {
        fetch('/chareit/comment/get-comments').then(res => res.json()).then(result => {
            this.setState({
                'comments': result.comments
            });
        });
    }

    getComments() {
        let comments = this.state.comments;
        let commentsItems = [];
        for (let comment in comments) {
            const item = comments[comment];
            commentsItems.push(React.createElement(
                'li',
                { key: item.id },
                item.message
            ));
        }
        return commentsItems;
    }

    render() {
        return React.createElement(
            'div',
            { className: 'comments' },
            React.createElement(
                'ul',
                null,
                this.getComments()
            )
        );
    }
}
export default Comment;