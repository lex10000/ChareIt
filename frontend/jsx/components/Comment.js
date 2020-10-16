class Comment extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            'comments': []
        }
    }

    componentDidMount() {
        fetch('/chareit/comment/get-comments')
            .then(res => res.json())
            .then((result) => {
                this.setState({
                    'comments': result.comments
                });
            })
    }

    getComments() {
        let comments = this.state.comments;
        let commentsItems = [];
        for (let comment in comments) {
            const item = comments[comment];
            commentsItems.push(<li key={item.id}>{item.message}</li>);
        }
        return commentsItems;
    }

    render() {
        return (
            <div className="comments">
                <ul>
                    {this.getComments()}
                </ul>
            </div>
        );
    }
}
export default Comment;