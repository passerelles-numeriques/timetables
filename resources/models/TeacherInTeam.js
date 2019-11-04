/** Class allowing to describe a teacher in a team */
export default class TeacherInTeam {
  /**
   * Default constructor
   * @param {String} name Name of the team
   * @param {String} color HTML color (hex of text)
   * @param {Array} teachers array of TeachersInTeam
   */
  constructor(name = '', color = '', calendar = '') {
    this.name = name;
    this.color = color;
    this.calendar = calendar;
  }

  /** Test the equality of two teams with a deep comparison of the array of teachers */
  equals(teacher) {
    return this.name === teacher.name &&
      this.color === teacher.color &&
      this.calendar === teacher.calendar;
  }
}
