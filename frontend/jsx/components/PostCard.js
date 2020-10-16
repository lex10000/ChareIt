import Comment from "./Comment.js";
import LikedUsers from "./LikedUsers.js";

export default class PostCard extends React.Component {
    constructor(props) {
        super(props);
        this.likePost = this.likePost.bind(this);
        this.deletePost = this.deletePost.bind(this);
        this.changeLikedPopup = this.changeLikedPopup.bind(this);
        this.state = {
            showComments: false,
            showLikedUsers: false,
            post: this.props.post,
            likedUsers: ''
        }
    }

    likePost(e) {
        e.preventDefault();
        fetch('/chareit/default/like', {
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
                    let post = this.state.post;
                    post.likesCount = result.likedUsers.length;
                    post.isLikedByUser = !post.isLikedByUser;
                    this.setState({
                        post: post,
                        showLikedUsers: true,
                        likedUsers: result.likedUsers
                    })
                }
            })
    }

    deletePost(event, id) {
        event.preventDefault();
        fetch('/chareit/default/delete-post', {
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
                    this.props.deletePost(id);
                }
            })
    }

   changeLikedPopup(status) {
        this.setState({
            showLikedUsers: status
        })
    }

    componentDidMount(){
        fetch('/chareit/default/get-liked-users', {
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
                    this.setState({
                        likedUsers: result.likedUsers
                    })
                }
            })
    }

    render() {
        let post = this.state.post;
        return (
            <div className="card post-card" onMouseLeave={() => this.changeLikedPopup(false)}>
                <div className="card-header">
                    <div className="card-header__userinfo">
                        <div className="card-header__avatar circle">
                            <a className="" href={`/profile/${post.user_id}`}>
                                <img className="" src={`/profile_avatars/thumbnails/${post.picture}`}
                                     alt=""/>
                            </a>
                        </div>
                        <div>
                            <a className="card-header__username"
                               href={`/profile/${post.user_id}`}>{post.username}</a>
                            <div
                                className="card-header__created_at">{post.date}
                            </div>
                        </div>
                    </div>
                    <div>
                        {post.isOwner &&
                        <a
                            href=""
                            onClick={(event) => {
                                this.deletePost(event, post.id)
                            }}
                            title="Удалить пост">
                            <i className="material-icons">clear</i>
                        </a>
                        }
                    </div>
                </div>
                <div className="card-image">
                    <img className="materialboxed" src={`/uploads/thumbnails/${post.filename}`}
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
                            <span className="likes-counter">{post.likesCount}</span>
                        </a>
                        {this.state.showLikedUsers &&
                        <LikedUsers likedUsers={this.state.likedUsers} closeLikedPopup={this.changeLikedPopup}/>
                        }
                    </div>
                    <div>
                        <a href={post.filename} download title="скачать оригинал">
                            <i className="material-icons">file_download</i>
                        </a>
                    </div>
                </div>
            </div>
        );
    }
}

