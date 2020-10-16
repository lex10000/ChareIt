
import Comment from "./Comment.js";
import LikedUsers from "./LikedUsers.js";

export default class PostCard extends React.Component
{
    constructor(props) {
        super(props);
        this.deletePost = this.deletePost.bind(this);
        this.state = {
            isLoaded: false,
            post : [],
            showComments: false,
            showLikedUsers: false,
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
    deletePost(e){
        this.setState({
            showComments: true,
        })
    }

    componentDidMount() {
        fetch('/chareit/default/get-one-post')
            .then(res => res.json())
            .then((result) => {
                this.setState({
                    post: result,
                    isLoaded: true
                })
            })
    }
    render() {
        if (!this.state.isLoaded) {
            return <div>Загрузка...</div>;
        } else {
            return (
                <div className="card post-card" data-target={this.state.post.id}>
                    <div className="card-header">
                        <div className="card-header__userinfo">
                            <div className="card-header__avatar circle">
                                <a className="" href={`/profile/${this.state.post.userId}`}>
                                    <img className="" src="" alt=""/>
                                </a>
                            </div>
                            <div>
                                <a className="card-header__username"
                                   href={`/profile/${this.state.post.userId}`}>{this.state.post.username}</a>
                                <div
                                    className="card-header__created_at">{this.state.post.date}
                                </div>
                            </div>
                        </div>
                        <div className="">
                            <div onClick={this.deletePost}
                                 data-target={this.state.post.userId}
                                 title="Удалить пост">
                                <i className="material-icons">clear</i>
                            </div>
                        </div>
                    </div>
                    <div className="card-image">
                        <img className="materialboxed" src={`/uploads/thumbnails/'${this.state.post.image}`} />
                    </div>
                    <div className="card-content">
                        <p>{this.state.post.description}</p>
                        {this.state.showComments && <Comment />}
                    </div>
                    <div className="liked-users">
                        {!this.state.showLikedUsers && <LikedUsers postId={this.state.post.id} csrfToken={this.state.csrfToken}/>}
                    </div>
                </div>
            );
        }
    }
}

