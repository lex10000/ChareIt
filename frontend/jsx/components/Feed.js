import PostCard from "./PostCard.js";

class Feed extends React.Component {

    constructor(props) {
        super(props);
        this.getPosts = this.getPosts.bind(this);
        this.deletePost = this.deletePost.bind(this);
        this.state = {
            posts: [],
            isLoaded:false
        }
    }

    getPosts(startPage = undefined) {
        const url = startPage ? '/get-feed/' + startPage : '/get-feed/';
        fetch(url)
            .then(res => res.json())
            .then((result) => {
                let posts = this.state.posts;
                posts.push(...result.posts);
                this.setState({
                    posts: posts,
                    isLoaded:true
                })
            });
    }

    deletePost(id) {
        let posts = this.state.posts;
        const index = posts.findIndex((post) => {
            return post.id === id
        });
        posts.splice(index, 1);
        this.setState({
            posts: posts
        })
    }

    componentDidMount() {
        this.getPosts();
    }

    render() {
        if(this.state.isLoaded) {
            return (
                <div>
                    {this.state.posts.map(post => (
                        <PostCard
                            key={post.id}
                            post={post}
                            deletePost={this.deletePost}
                        />
                    ))}
                </div>
            );
        } else {
            return <div>Загружаю..</div>;
        }
    }
}

export default Feed;