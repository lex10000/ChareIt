import Comments from "./Comment.js";
import LikedUsers from "./LikedUsers.js";

export default class PostCard extends React.Component {
    constructor(props) {
        super(props);
        this.likePost = this.likePost.bind(this);
        this.deletePost = this.deletePost.bind(this);
        this.changeLikedPopup = this.changeLikedPopup.bind(this);
        this.showComments = this.showComments.bind(this);
        this.state = {
            showComments: false,
            showLikedUsers: false,
        }
    }

    likePost(e) {
        e.preventDefault();
        fetch('/chareit/post/like-post', {
            method: 'POST',
            headers: {
                'content-type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({'postId': this.props.post.id}),

        })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    this.props.likePost(this.props.post.id, result.likedUsers);
                    this.setState({
                        showLikedUsers: true
                    })
                }
            })
    }

    deletePost(event) {
        event.preventDefault();
        fetch('/chareit/post/delete-post', {
            method: 'POST',
            headers: {
                'content-type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({'postId': this.props.post.id}),
        })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    M.toast({html: 'Пост удален!'});
                    this.props.deletePost(this.props.post.id);
                }
            })
    }

   changeLikedPopup(status) {
        status !== this.state.showLikedUsers &&
        this.setState({
            showLikedUsers: status
        })
    }

    showComments(e){
        e.preventDefault();
        this.setState({
            showComments :true
        })
    }

    render() {
        let post = this.props.post;
        return (
            <div className="card post-card" onMouseLeave={() => this.changeLikedPopup(false)} onTouchMove={() => this.changeLikedPopup(false)}>
                <div className="card-header" >
                    <div className="card-header__userinfo">
                        <div className="card-header__avatar circle">
                            <a className="" href={`/profile/${post.user_id}`}>
                                <img className="" src={`/profile_avatars/thumbnails/${post.picture}`} alt=""/>
                            </a>
                        </div>
                        <div>
                            <a className="card-header__username" href={`/profile/${post.user_id}`}>{post.username}</a>
                            <div
                                className="card-header__created_at">{post.date}
                            </div>
                        </div>
                    </div>
                    <div>
                        {post.isOwner &&
                        <a
                            href=""
                            onClick={this.deletePost}
                            title="Удалить пост">
                            <i className="material-icons">clear</i>
                        </a>
                        }
                    </div>
                </div>
                <div className="card-image">
                    <img src={`/uploads/thumbnails/${post.filename}`}
                         alt='здесь был пост...'/>
                </div>
                <div className="card-content">
                    <p>{post.description}</p>
                </div>
                <div className="card-action">
                    <div>
                        <a href=""
                           onClick={this.likePost}
                           onMouseEnter={() => this.changeLikedPopup(true)}
                           title="Поставить лайк">
                            <i className="material-icons">
                                {post.isLikedByUser ? 'favorite' : 'favorite_border'}
                            </i>
                            <span className="likes-counter">{post.likedUsers.length}</span>
                        </a>
                        {this.state.showLikedUsers &&
                        <LikedUsers likedUsers={post.likedUsers} closeLikedPopup={this.changeLikedPopup}/>
                        }
                        {/*<a href="" onClick={this.showComments}><i className="material-icons">chat_bubble</i><span>33</span></a>*/}
                    </div>
                    <div>
                        <a href={post.filename} download title="скачать оригинал">
                            <i className="material-icons">file_download</i>
                        </a>
                    </div>
                </div>
                {/*{this.state.showComments &&*/}
                {/*<Comments postId={this.props.post.id}/>*/}
                {/*}*/}
            </div>
        );
    }
}

