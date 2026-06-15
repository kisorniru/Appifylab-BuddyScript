import AssetImage from '../common/AssetImage.jsx';
import { desktopStories, exploreItems, mobileStoryImages, people } from '../../data/feedStaticData.js';
import { ExploreIcon, OnlineDot, SearchIcon } from './FeedIcons.jsx';

export function LeftSidebar() {
  return (
    <div className="_layout_left_sidebar_wrap">
      <div className="_layout_left_sidebar_inner">
        <div className="_left_inner_area_explore _padd_t24 _padd_b6 _padd_r24 _padd_l24 _b_radious6 _feed_inner_area">
          <h4 className="_left_inner_area_explore_title _title5 _mar_b24">Explore</h4>
          <ul className="_left_inner_area_explore_list">
            {exploreItems.map((item, index) => (
              <li className={`_left_inner_area_explore_item${index === 0 || index === 5 ? ' _explore_item' : ''}`} key={item}>
                <a href="#0" className="_left_inner_area_explore_link">
                  <ExploreIcon type={item} />
                  {item}
                </a>
                {(index === 0 || index === 5) && <span className="_left_inner_area_explore_link_txt">New</span>}
              </li>
            ))}
          </ul>
        </div>
      </div>

      <div className="_layout_left_sidebar_inner">
        <div className="_left_inner_area_suggest _padd_t24 _padd_b6 _padd_r24 _padd_l24 _b_radious6 _feed_inner_area">
          <div className="_left_inner_area_suggest_content _mar_b24">
            <h4 className="_left_inner_area_suggest_content_title _title5">Suggested People</h4>
            <span className="_left_inner_area_suggest_content_txt">
              <a className="_left_inner_area_suggest_content_txt_link" href="#0">See All</a>
            </span>
          </div>
          {people.slice(0, 3).map((person, index) => (
            <div className="_left_inner_area_suggest_info" key={person.name}>
              <div className="_left_inner_area_suggest_info_box">
                <div className="_left_inner_area_suggest_info_image">
                  <a href="#0">
                    <AssetImage name={person.image} alt={person.name} className={index === 0 ? '_info_img' : '_info_img1'} />
                  </a>
                </div>
                <div className="_left_inner_area_suggest_info_txt">
                  <a href="#0"><h4 className="_left_inner_area_suggest_info_title">{person.name}</h4></a>
                  <p className="_left_inner_area_suggest_info_para">{person.role}</p>
                </div>
              </div>
              <div className="_left_inner_area_suggest_info_link">
                <a href="#0" className="_info_link">Connect</a>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="_layout_left_sidebar_inner">
        <div className="_left_inner_area_event _padd_t24 _padd_b6 _padd_r24 _padd_l24 _b_radious6 _feed_inner_area">
          <div className="_left_inner_event_content">
            <h4 className="_left_inner_event_title _title5">Events</h4>
            <a href="#0" className="_left_inner_event_link">See all</a>
          </div>
          {[1, 2].map((event) => (
            <a className="_left_inner_event_card_link" href="#0" key={event}>
              <div className="_left_inner_event_card">
                <div className="_left_inner_event_card_iamge">
                  <AssetImage name="feed_event1.png" alt="Event" className="_card_img" />
                </div>
                <div className="_left_inner_event_card_content">
                  <div className="_left_inner_card_date">
                    <p className="_left_inner_card_date_para">10</p>
                    <p className="_left_inner_card_date_para1">Jul</p>
                  </div>
                  <div className="_left_inner_card_txt">
                    <h4 className="_left_inner_event_card_title">No more terrorism no more cry</h4>
                  </div>
                </div>
                <hr className="_underline" />
                <div className="_left_inner_event_bottom">
                  <p className="_left_iner_event_bottom">17 People Going</p>
                  <span className="_left_iner_event_bottom_link">Going</span>
                </div>
              </div>
            </a>
          ))}
        </div>
      </div>
    </div>
  );
}

export function Stories() {
  return (
    <>
      <div className="_feed_inner_ppl_card _mar_b16">
        <div className="_feed_inner_story_arrow">
          <button type="button" className="_feed_inner_story_arrow_btn">&gt;</button>
        </div>
        <div className="row">
          {desktopStories.map((story) => (
            <div className={`col-xl-3 col-lg-3 col-md-4 col-sm-4 col${story.hideMobile ? ' _custom_mobile_none' : ''}${story.hideTablet ? ' _custom_none' : ''}`} key={story.image}>
              <div className={`${story.own ? '_feed_inner_profile_story' : '_feed_inner_public_story'} _b_radious6`}>
                <div className={story.own ? '_feed_inner_profile_story_image' : '_feed_inner_public_story_image'}>
                  <AssetImage name={story.image} alt={story.name} className={story.own ? '_profile_story_img' : '_public_story_img'} />
                  {story.own ? (
                    <div className="_feed_inner_story_txt">
                      <div className="_feed_inner_story_btn">
                        <button type="button" className="_feed_inner_story_btn_link">+</button>
                      </div>
                      <p className="_feed_inner_story_para">{story.name}</p>
                    </div>
                  ) : (
                    <>
                      <div className="_feed_inner_pulic_story_txt">
                        <p className="_feed_inner_pulic_story_para">{story.name}</p>
                      </div>
                      <div className="_feed_inner_public_mini">
                        <AssetImage name="mini_pic.png" alt="" className="_public_mini_img" />
                      </div>
                    </>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="_feed_inner_ppl_card_mobile _mar_b16">
        <div className="_feed_inner_ppl_card_area">
          <ul className="_feed_inner_ppl_card_area_list">
            {mobileStoryImages.map((image, index) => (
              <li className="_feed_inner_ppl_card_area_item" key={`${image}-${index}`}>
                <a href="#0" className="_feed_inner_ppl_card_area_link">
                  <div className={index === 0 ? '_feed_inner_ppl_card_area_story' : index % 2 ? '_feed_inner_ppl_card_area_story_active' : '_feed_inner_ppl_card_area_story_inactive'}>
                    <AssetImage name={image} alt="Story" className={index === 0 ? '_card_story_img' : '_card_story_img1'} />
                    {index === 0 && (
                      <div className="_feed_inner_ppl_btn">
                        <button className="_feed_inner_ppl_btn_link" type="button" aria-label="Upload story">
                          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12">
                            <path stroke="#fff" strokeLinecap="round" strokeLinejoin="round" d="M6 2.5v7M2.5 6h7" />
                          </svg>
                        </button>
                      </div>
                    )}
                  </div>
                  <p className={index === 0 ? '_feed_inner_ppl_card_area_link_txt' : '_feed_inner_ppl_card_area_txt'}>{index === 0 ? 'Your Story' : 'Ryan...'}</p>
                </a>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </>
  );
}

export function RightSidebar() {
  return (
    <div className="_layout_right_sidebar_wrap">
      <div className="_layout_right_sidebar_inner">
        <div className="_right_inner_area_info _padd_t24 _padd_b24 _padd_r24 _padd_l24 _b_radious6 _feed_inner_area">
          <div className="_right_inner_area_info_content _mar_b24">
            <h4 className="_right_inner_area_info_content_title _title5">You Might Like</h4>
            <span className="_right_inner_area_info_content_txt"><a className="_right_inner_area_info_content_txt_link" href="#0">See All</a></span>
          </div>
          <hr className="_underline" />
          <div className="_right_inner_area_info_ppl">
            <div className="_right_inner_area_info_box">
              <div className="_right_inner_area_info_box_image">
                <a href="#0"><AssetImage name="Avatar.png" alt="Radovan SkillArena" className="_ppl_img" /></a>
              </div>
              <div className="_right_inner_area_info_box_txt">
                <a href="#0"><h4 className="_right_inner_area_info_box_title">Radovan SkillArena</h4></a>
                <p className="_right_inner_area_info_box_para">Founder & CEO at Trophy</p>
              </div>
            </div>
            <div className="_right_info_btn_grp">
              <button type="button" className="_right_info_btn_link">Ignore</button>
              <button type="button" className="_right_info_btn_link _right_info_btn_link_active">Follow</button>
            </div>
          </div>
        </div>
      </div>

      <div className="_layout_right_sidebar_inner">
        <div className="_feed_right_inner_area_card _padd_t24 _padd_b6 _padd_r24 _padd_l24 _b_radious6 _feed_inner_area">
          <div className="_feed_top_fixed">
            <div className="_feed_right_inner_area_card_content _mar_b24">
              <h4 className="_feed_right_inner_area_card_content_title _title5">Your Friends</h4>
              <span className="_feed_right_inner_area_card_content_txt"><a className="_feed_right_inner_area_card_content_txt_link" href="#0">See All</a></span>
            </div>
            <form className="_feed_right_inner_area_card_form">
              <SearchIcon className="_feed_right_inner_area_card_form_svg" />
              <input className="form-control me-2 _feed_right_inner_area_card_form_inpt" type="search" placeholder="input search text" aria-label="Search" />
            </form>
          </div>
          <div className="_feed_bottom_fixed">
            {people.map((person, index) => (
              <div className={`_feed_right_inner_area_card_ppl${person.inactive ? ' _feed_right_inner_area_card_ppl_inactive' : ''}`} key={`${person.name}-${index}`}>
                <div className="_feed_right_inner_area_card_ppl_box">
                  <div className="_feed_right_inner_area_card_ppl_image">
                    <a href="#0"><AssetImage name={person.image} alt={person.name} className="_box_ppl_img" /></a>
                  </div>
                  <div className="_feed_right_inner_area_card_ppl_txt">
                    <a href="#0"><h4 className="_feed_right_inner_area_card_ppl_title">{person.name}</h4></a>
                    <p className="_feed_right_inner_area_card_ppl_para">{person.role}</p>
                  </div>
                </div>
                <div className="_feed_right_inner_area_card_ppl_side">
                  {person.inactive ? <span>5 minute ago</span> : <OnlineDot />}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
