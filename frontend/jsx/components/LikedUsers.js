class LikedUsers extends React.Component {

    constructor(props) {
        super(props);
        this.closeLikedPopup = this.closeLikedPopup.bind(this);
    }

    closeLikedPopup(event = undefined) {
        event && event.preventDefault();
        this.props.closeLikedPopup(false);
    }

    render() {
        console.log(this.props);
        const likedUsers = this.props.likedUsers;
        return (
                <div className="liked-users__container" onMouseLeave={this.closeLikedPopup}>
                    <div className="liked-users__header">
                        {(likedUsers.length === 0) ? 'Никто не лайкнул' : 'Понравилось: ' + likedUsers.length}
                    </div>
                    <a href=""
                       onClick={(event) => this.closeLikedPopup(event)}
                       className="liked-users__close"
                       title='Закрыть окно'>
                        <i className="material-icons">close</i>
                    </a>
                    <div className="liked-users__cards">
                        {(likedUsers.length > 0) &&
                        likedUsers.map(user => {
                            return (
                                <div className='liked-users__card' key={user.id}>
                                    <a className="liked-users__avatar circle" href={`/profile/${user.id}`}
                                       title={user.username}>
                                        <img src={user.picture} alt="здесь была аватарка.."/>
                                    </a>
                                </div>
                            )
                        })
                        }
                    </div>
                </div>
        )
    }
}

export default LikedUsers;