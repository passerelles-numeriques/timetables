/** Class allowing to group teachers in a team */
export default class Team {
  /**
   * Default constructor
   * @param {String} name Name of the team, any string 
   * @param {Array} teachers array of TeachersInTeam
   */
  constructor(name = '', teachers = []) {
    this.name = name;
    this.teachers = teachers;
  }

  /** Test the equality of two teams with a deep comparison of the array of teachers */
  equals(team) {
    return this.name === team.name &&
      this.teachers.length === team.teachers.length &&
      this.teachers.sort().every(function(value, index) { return value === team.teachers.sort()[index]});
  }
}
