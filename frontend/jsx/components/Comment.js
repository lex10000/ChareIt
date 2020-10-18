class Comments extends React.Component {
    constructor(props) {
        super(props);
        this.createMessage = this.createMessage.bind(this);
        this.getComments = this.getComments.bind(this);
        this.state = {
            'comments': [],
            'isLoaded': false,
            'message' : '',
            'validateError' : '',
            'isEmpty':false
        }
    }

    componentDidMount() {
        this.getComments();
    }

    getComments(offset = 0){
        const url = (offset!==0)
            ? '/chareit/comment/get-comments?postId=' + this.props.postId + '&offset=' + offset
            : '/chareit/comment/get-comments?postId=' + this.props.postId
        fetch(url)
            .then(res => res.json())
            .then((result) => {
                if(result.status === 'success') {
                    this.setState({
                        'comments': [...this.state.comments, ...result.comments],
                        'isLoaded': true,
                    });
                } else {
                    this.setState({
                        'isEmpty': true,
                        'isLoaded': true,
                    });
                }
            })
    }
    createMessage(e){
        e.preventDefault();
        if(this.validateMessage(this.state.message)) {
            fetch('/chareit/comment/create-comment', {
                method: 'POST',
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({'postId': this.props.postId, 'message' : this.state.message.trim()})
            })
                .then(res => res.json())
                .then(result => {
                    if(result.status === 'success') {
                        this.setState({
                            'comments': [...this.state.comments, result.comment],
                            'message': '',
                            'validateError': '',
                            'isEmpty': false
                        })
                    } else {
                        this.setState({
                            'validateError': result.status,
                            'message': '',
                        })
                    }

                })

        } else {
            this.setState({
                validateError: 'Сообщение не прошло валидацию'
            })
        }
    }

    deleteMessage(){

    }

    validateMessage(message) {
        //return (message.length > 0 && message.length < 255)
        return true
    }

    render() {
        if(this.state.isLoaded) {
            if(this.state.isEmpty) {
                return (
                    <div className="comments">
                        <form onSubmit={this.createMessage}>
                            <input type="text" name='input-message' value={this.state.message} onChange={(e) => this.setState({'message': e.target.value.trim()})}/>
                            <button type='submit' className='btn'><i className="material-icons prefix">mode_edit</i></button>
                        </form>
                        <div>Комментариев нет</div>
                    </div>
                     )
            } else {
                return (
                    <div className="comments">
                        <form onSubmit={this.createMessage}>
                            <input type="text" name='input-message' onChange={(e) => this.setState({'message': e.target.value})}/>
                            <button type='submit' className='btn'><i className="material-icons prefix">mode_edit</i></button>
                        </form>
                        {this.state.validateError &&
                        <div className='validate__error'>{this.state.validateError}</div>
                        }
                        {this.state.comments.map(comment => {
                            return (<div className="comment" key={comment.id}>
                                <a href={`/profile/${comment.user_id}`} className="comment__avatar circle">
                                    <img src={`/profile_avatars/thumbnails/${comment.picture}`} alt=""/>
                                </a>
                                <div>
                                    <a href={`/profile/${comment.user_id}`} className="comment__username">{comment.username}</a>
                                    <div className="comment__message">{comment.comment}</div>
                                    <div className="comment__date">{comment.date}</div>
                                </div>
                            </div>)
                        })}
                    </div>
                );
            }
        }
        else return null;
    }
}
export default Comments;