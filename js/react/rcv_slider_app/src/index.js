import React, { Component } from "react";
import ReactDOM from "react-dom";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import uuid from 'react-uuid';
// import { Redirect } from 'react-router-dom';
import './app.css';
import { ImmortalDB } from 'immortal-db';

const candidates = [
  {id: `1`, content: `Candidates`}
];





// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);

  return result;
};

const grid = 8;

const getItemStyle = (isDragging, draggableStyle) => ({
  // some basic styles to make the items look a bit nicer
  userSelect: "none",
  padding: grid * 1.5,
  borderRadius: `0 4px 4px 0`,
  borderLeft: `solid 8px #d30ca1`,
  margin: `0 0 ${grid}px 0`,
  fontFamily: "'Inter', sans-serif",
  fontWeight: `bold`,
  color: '#666666',
  fontSize: `18px`,
  listStyle: `none`,
  touchAction: `none`,

  // change background colour if dragging
  background: isDragging ? "#ffffff" : "rgb(253,253,253) linear-gradient(180deg, rgba(253,253,253,1) 0%, rgba(242,242,242,1) 100%)",
  boxShadow: isDragging ? `0 2px 8px 0 rgba(0, 0, 0, 0.5)` : `0 1px 4px 0 rgba(0, 0, 0, 0.5)`,

  // styles we need to apply on draggables
  ...draggableStyle
});


const getListStyle = (isDragging, isDraggingOver) => ({
  // background: isDraggingOver ? "#ffffff" : "#ffffff",
  padding: `16px 0 8px 0`,
  margin: `8px 0 16px 0`,
  listStyle: `none`,
  touchAction: `none`,
  width: `92%`
});



class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      items: candidates
    };
    this.onDragEnd = this.onDragEnd.bind(this);



  }


  async componentDidMount() {

    const candidatearray = drupalSettings.rcv_slider_app.candidatelist;

    candidatearray.sort(() => 0.5 - Math.random());

    let candidatesrev = [];
    for(let i = 0; i < candidatearray.length; i++) {
      let keyid = 'key-' + (i+1);
      candidatesrev.push({id:keyid, content:candidatearray[i]['value']});
    }


    const passednodeid = drupalSettings.rcv_slider_app.reactNID;
    const resultspage = "/node/" + passednodeid + "/results";

    const storeduserid = await ImmortalDB.get('pollrespid')
    const storedpollid = await ImmortalDB.get('pollid')

    if (storeduserid !== null && storedpollid == passednodeid) {
      window.location.href=resultspage;
    }

    this.setState({
      items: candidatesrev,
      loading: false,
      passednodeid: passednodeid
    });
  }






  // Record choices to server via JSON API.
  async logchoices() {

    let payload = JSON.stringify(this.state.items);

    // Collect the identify of this poll
    const passednode = this.state.passednodeid;

    // Build the URL for the results page
    const resultspage = "/node/" + this.state.passednodeid + "/results";


    // Check if the person has voted before.
    // If they have voted before, a UUID will be stored in local storage in their browser.
    // This can be defeated if the user deletes the value in local storage.
    // const storeduserid = localStorage.getItem('rcvrespid');
    // const storedpollid = localStorage.getItem('rcvpollid');

    const storeduserid = await ImmortalDB.get('pollrespid')
    const storedpollid = await ImmortalDB.get('pollid')


    // If there is no record of a previous vote, store the newly created UUID in browser's local storage.
    if (storeduserid !== null && storedpollid == passednode) {

      // If user has already voted, replace their previous vote with their new vote.

      let userid = storeduserid;

      // Build JSON API endpoint.
      const retrieveEndpoint = '/jsonapi/score/score' + '?filter[respondent_id][value]=' + storeduserid + '&filter[poll_id][value]=' + passednode;

      // This request finds their previous vote using their repspondent ID.
      // Their respondent ID is the UUID stored in their local storage.

      try {
        const response = await fetch(retrieveEndpoint,
          {
            method: 'GET',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/vnd.api+json',
              'Authorization': 'Basic {basic auth code here}'
            }});
        if (!response.ok) {
          throw Error(response.statusText);
        }
        const loadedvote = await response.json();
        let loadedvotearray = loadedvote["data"];

        let loadedvoteobj = loadedvotearray[0];

        let votetofix = loadedvoteobj.id;


        // Build JSON API endpoint for the PATCH (update data) request.
        const updateEndpoint = '/jsonapi/score/score/' +  votetofix;

        // Do the update.
        const updateRecord = await fetch(updateEndpoint,
          {
            method: 'PATCH',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/vnd.api+json',
              'Authorization': 'Basic {basic auth code here}'
            },
            body: JSON.stringify(
              {"data": {
                  "type": "score--score",
                  "id": votetofix,
                  "attributes": {
                    "choice_list": payload,
                  }
                }}
            )
          })
          .then((res) => {
            if (res.ok) {
              res.json()
                .then(console.log("Update made."))
                .then(window.location.href=resultspage);
            }
            else {
              console.log('Could not update.');
              window.location.href=resultspage;
            }
          });





      } catch (error) {
        console.log(error);
      }


    } else {

      // Create a UUID to identify the voter.
      let userid = uuid();

      // Get set to log the vote.
      const endpoint = '/jsonapi/score/score';
      const method = 'POST';

      await fetch("https://extreme-ip-lookup.com/json")
        .then(res => res.json())
        .then(res => { // call this function when the above chained Promise resolves
          this.setState({
            ip_address_found: res.query,
            placename: res.city
          });
        });


      // Store the voter's preferences in the Drupal database, in the score table.
      await fetch(endpoint, {
        method: method,
        credentials: 'include',
        headers: {
          'Accept': 'application/vnd.api+json',
          'Content-Type': 'application/vnd.api+json',
          'Authorization': 'Basic {basic auth code here}',
        },
        body: JSON.stringify(
    {"data": {
            "type": "score--score",
            "attributes": {
              "name": "Ranked result",
              "respondent_id": userid,
              "choice_list": payload,
              "poll_id": passednode,
              "locate": this.state.placename,
              "ip_address": this.state.ip_address_found
        }
      }
    }
    )})
.then((response) => {
  if (response.ok) {
    // Store the UUID in the voters browser.
    ImmortalDB.set('pollrespid', userid)
    ImmortalDB.set('pollid', this.state.passednodeid)

    // localStorage.setItem('rcvrespid', userid);
    // localStorage.setItem('rcvpollid', this.state.passednodeid);
    console.log("Ranking recorded.");
    window.location.href=resultspage;
  }
  else {
    console.log('Error recording choice.');
    window.location.href=resultspage;
  }
});



}}



  onDragStart = (start, provided) => {

    const candidateList = this.state.items;
    const namematch = candidateList.filter(candidate => candidate.id == start.draggableId);

    provided.announce(
      `You have lifted ${namematch[0].content} in position ${start.source.index + 1}`
    );
  };

  onDragUpdate = (update, provided) => {



    const message = update.destination
      ? `You have moved the choice to position ${update.destination.index + 1}`
      : `You are currently not over a droppable area`;

    provided.announce(message);
  };




  onDragEnd(result, provided) {

    const candidateList = this.state.items;
    const namematch = candidateList.filter(candidate => candidate.id == result.draggableId);

    // dropped outside the list
    if (!result.destination) {
      return;
    }

    const items = reorder(
      this.state.items,
      result.source.index,
      result.destination.index
    );

    this.setState({
      items
    });

    const message = result.destination
      ? `You have dropped the item. You have moved ${namematch[0].content} from position
        ${result.source.index + 1} to ${result.destination.index + 1}`
      : `The choice has been returned to its starting position of
        ${result.source.index + 1}`;

    provided.announce(message);

    const { destination, source, draggableId, type } = result;

  }




  render() {
    return (
      <DragDropContext
        onDragStart={this.onDragStart}
        onDragUpdate={this.onDragUpdate}
        onDragEnd={this.onDragEnd}
      >
        <Droppable droppableId="droppable">
          {(provided, snapshot) => (
            <div
              role="group"
              {...provided.droppableProps}
              ref={provided.innerRef}
              style={getListStyle(snapshot.isDraggingOver)}
            >
              {this.state.items.map((item, index) => (
                <Draggable key={item.id} draggableId={item.id} index={index}>
                  {(provided, snapshot) => (
                    <div
                      role="button"
                      ref={provided.innerRef}
                      {...provided.draggableProps}
                      {...provided.dragHandleProps}
                      style={getItemStyle(
                        snapshot.isDragging,
                        provided.draggableProps.style
                      )}
                    >
                      {item.content}
                    </div>
                  )}
                </Draggable>
              ))}
              {provided.placeholder}
            </div>
          )}
        </Droppable>
        <button onClick={() => this.logchoices()}>Record Vote</button>
      </DragDropContext>
    );
  }

}

// Put the thing into the DOM!
ReactDOM.render(<App />, document.getElementById("root"));
