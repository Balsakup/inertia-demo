import React from 'react';
import {ChevronLeft, ChevronRight, Play} from 'lucide-react';
import PropTypes from 'prop-types';

class HomePage extends React.Component {
    constructor(props) {
        super(props);
    }

    scrollLeft(event, scrollable) {
        event.preventDefault();

        const scrollableElement = document.querySelector(`[data-scroll="${scrollable}"]`);
        const scrollableScrollLeft = scrollableElement.scrollLeft;

        scrollableElement.scroll({
            left: scrollableScrollLeft - scrollableElement.firstChild.clientWidth,
            behavior: 'smooth'
        });
    }

    scrollRight(event, scrollable) {
        event.preventDefault();

        const scrollableElement = document.querySelector(`[data-scroll="${scrollable}"]`);
        const scrollableScrollLeft = scrollableElement.scrollLeft;

        scrollableElement.scroll({
            left: scrollableScrollLeft + scrollableElement.firstChild.clientWidth,
            behavior: 'smooth'
        });
    }

    render() {
        return (
            <>
                {this.props.songTypes.map((songType) => (
                    <div className="mb-4" key={`home-page-song-type-${songType.id}`}>
                        <div className="flex justify-between items-center m-4 mb-2">
                            <h2 className="text-white font-bold text-2xl">{songType.name}</h2>
                            <div className="flex items-center">
                                <a href="#"
                                   className="mx-2 stroke-zinc-400 hover:stroke-green-500"
                                   onClick={(event) => this.scrollLeft(event, `songs-scrollable-${songType.id}`)}>
                                    <ChevronLeft size={16} className="stroke-inherit"/>
                                </a>
                                <a href="#"
                                   className="mx-2 stroke-zinc-400 hover:stroke-green-500"
                                   onClick={(event) => this.scrollRight(event, `songs-scrollable-${songType.id}`)}>
                                    <ChevronRight size={16} className="stroke-inherit"/>
                                </a>
                                <a href=""
                                   className="text-zinc-400 hover:text-green-500 hover:opacity-50 text-xs font-bold uppercase transition transition-color duration-300">
                                    Voir tout
                                </a>
                            </div>
                        </div>
                        <div className="flex overflow-x-scroll snap-x snap-mandatory scrollbar-hide"
                             data-scroll={`songs-scrollable-${songType.id}`}>
                            {songType.latest_songs.map((song) => (
                                <div className="p-4 snap-start" key={`home-page-song-${song.id}`}>
                                    <div className="flex-none relative group w-48 bg-zinc-700 p-4 rounded">
                                        <img src={song.cover.normal} alt={song.name}/>
                                        <h3 className="text-white font-bold my-2">{song.name}</h3>
                                        <p className="text-sm text-zinc-400">{song.description.substring(0, 40)}...</p>
                                        <button className="opacity-0 group-hover:opacity-100 flex justify-center items-center absolute right-0 bottom-0 -translate-y-1/4 -translate-x-1/4 w-10 h-10 rounded-full bg-green-500 transition duration-300">
                                            <Play size={16} className="stroke-white fill-white"/>
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                ))}
            </>
        );
    }
}

HomePage.propTypes = {
    songTypes: PropTypes.array.isRequired
};

export default HomePage;
