const { Component } = wp.element;

export default class ArticleExample extends Component {
    constructor() {
        super( ...arguments );
    }

    render() {

        const { attributes, count } = this.props;
        const { 
            layout,
            borderColor,
            backgroundColor,
            textColor,
            decorColor,
            dateColor,
         } = attributes;
        
        const activeDirectoryPlugin = typeof CitadelaDirectorySettings !== 'undefined';

        //styles
        let thumbnailStyle = {
            ...( decorColor ? { color: decorColor } : {} ),
        };


        let articleStyle = {
            ...( layout == "simple" && textColor ? { color: textColor } : {} ),
            ...( layout == "simple" && backgroundColor ? { backgroundColor: backgroundColor } : {} ),
            ...( layout == "simple" && borderColor ? { borderColor: borderColor } : {} ),
        };
        

        let itemContentStyle = {
            ...( layout != "simple" && textColor ? { color: textColor } : {} ),
            ...( layout != "simple" && backgroundColor ? { backgroundColor: backgroundColor } : {} ),
            ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
        };

        let footerStyle = {
            ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
        };

        let itemDataStyle = {
            ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
            ...( decorColor ? { color: decorColor } : {} ),
            ...( decorColor ? { borderColor: decorColor } : {} ),
        };
        let dateStyle = {
            ...( layout == "list" && decorColor ? { color: decorColor } : {} ),
            ...( layout == "box" && decorColor ? { backgroundColor: decorColor } : {} ),
            ...( layout == "box" && dateColor ? { color: dateColor } : {} ),
        };

        let entryMeta = {
            ...( layout == "simple" && decorColor ? { color: decorColor } : {} ),
        };

        if (layout == "simple") {
            return (
                <div class="citadela-block-articles-wrap">
                    {Array.from(Array(count), (e, i) => {
                        return (
                            <article class="citadela-directory-item" style={ articleStyle }>
                                <div class="item-content" style={ itemContentStyle }>
                                    
                                    <div class="item-title">
                                        <div class="item-title-wrap">
                                            <div class="post-title"></div>
                                            <div class="post-meta" style={ entryMeta }></div>
                                        </div>
                                    </div>
                                    
                                    <div class="item-thumbnail" style={ thumbnailStyle }></div>
                                    
                                    <div class="item-body">
                                        <div class="item-text">
                                            <div class="item-description">
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                            </div>
                                        </div>

                                        <div class="item-footer" style={ footerStyle }>
                                            { activeDirectoryPlugin &&
                                                <div class="item-data location" style={ itemDataStyle }>
                                                    <span class="label"></span>
                                                    <span class="values"></span>
                                                </div>
                                            }
                                            <div class="item-data categories" style={ itemDataStyle }>
                                                <span class="label"></span>
                                                <span class="values"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </article>
                        )
                    })}
                </div>
            );
        } else {
            return (
                <div class="citadela-block-articles-wrap">
                    {Array.from(Array(count), (e, i) => {
                        return (
                            <article class="citadela-directory-item" style={ articleStyle }>
                                <div class="item-content" style={ itemContentStyle }>
                                    <div class="item-thumbnail" style={ thumbnailStyle }>
                                        { layout == "box" &&
                                            <div class="item-date" style={ dateStyle }></div>
                                        }
                                    </div>
                                    <div class="item-body">
                                        <div class="item-title">
                                            <div class="item-title-wrap">
                                                { layout == "box" && ! attributes.showFeaturedImage  &&
                                                    <div class="item-date" style={ dateStyle }></div>
                                                }
                                                <div class="post-title"></div>
                                                { layout == "list" && ! attributes.showDescription  &&
                                                    <div class="item-date" style={ dateStyle }></div>
                                                }
                                                <div class="post-subtitle"></div>
                                            </div>
                                        </div>

                                        <div class="item-text">
                                            <div class="item-description">
                                                { layout == "list" && attributes.showDescription &&
                                                    <div class="item-date" style={ dateStyle }></div>
                                                }
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                            </div>
                                        </div>

                                        <div class="item-footer" style={ footerStyle }>
                                            <div class="item-data address" style={ itemDataStyle }>
                                                <span class="label"></span>
                                                <span class="values"></span>
                                            </div>

                                            <div class="item-data web" style={ itemDataStyle }>
                                                <span class="label"></span>
                                                <span class="values"></span>
                                            </div>

                                            { activeDirectoryPlugin &&
                                                <div class="item-data location" style={ itemDataStyle }>
                                                    <span class="label"></span>
                                                    <span class="values"></span>
                                                </div>
                                            }

                                            <div class="item-data categories" style={ itemDataStyle }>
                                                <span class="label"></span>
                                                <span class="values"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        )
                    })}
                </div>
            );
        }
    }
}

ArticleExample.defaultProps = {
    count: 12,
}