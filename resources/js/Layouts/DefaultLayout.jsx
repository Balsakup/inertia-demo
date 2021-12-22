import React from 'react';
import PropTypes from 'prop-types';

class DefaultLayout extends React.Component {
    render() {
        return (
            <div className="flex w-screen h-screen">
                <aside className="w-64 h-screen bg-zinc-900">

                </aside>

                <div className="flex-1 flex flex-col min-w-0 bg-zinc-800">
                    <div className="overflow-y-scroll">
                        {this.props.children}
                    </div>

                    <div className="h-32 bg-zinc-600 mt-auto p-4">
                        fef
                    </div>
                </div>
            </div>
        );
    }
}

DefaultLayout.propTypes = {
    children: PropTypes.object.isRequired
};

export default DefaultLayout;
